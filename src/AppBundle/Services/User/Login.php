<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-06-30
 * Time: 12:36
 */

namespace AppBundle\Services\User;


use AppBundle\Entity\User;
use AppBundle\Entity\WeixinProfile;
use AppBundle\Repository\UserRepository;
use AppBundle\Services\QiniuUploader;
use AppBundle\Services\RandomString;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\UserManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Validator\Constraints\DateTime;

class Login
{
    private $userRepository;
    private $userManager;
    private $entityManager;
    private $randomStringGenerator;
    private $qiniuUploader;
    private $encoderFactory;
    private $adminEmail;

    private $updateProfileImage;

    /**
     * Login constructor.
     * @param UserRepository $userRepository
     * @param UserManager $userManager
     * @param EntityManager $entityManager
     * @param QiniuUploader $qiniuUpoader
     * @param RandomString $randomStringGenerator
     * @param EncoderFactory $encoderFactory
     * @param $adminEmail
     */
    public function __construct($userRepository, $userManager, $entityManager, $qiniuUpoader, $randomStringGenerator, $encoderFactory, $adminEmail)
    {
        $this->userRepository = $userRepository;
        $this->userManager = $userManager;
        $this->entityManager = $entityManager;
        $this->randomStringGenerator = $randomStringGenerator;
        $this->qiniuUploader = $qiniuUpoader;
        $this->encoderFactory = $encoderFactory;
        $this->adminEmail = $adminEmail;
    }

    public function loginWeixin($openId, $weixinJson) {

        /** @var User $user */
        $user = $this->userRepository->findUserByOpenId($openId);
        if (!$user) {
            $user = $this->createUser($weixinJson->nickname, $weixinJson->headimgurl);
            $user->setWeixinAccount($this->createWeixinProfile($user, $weixinJson));
        }

        $user->setLastLoginType(User::LOGIN_TYPE['weixin']);

        return $this->login($user);
    }

    public function loginEmail($email, $plainPassword) {
        /** @var User $user */
        $user = $this->userManager->findUserByEmail($email);
        if (!$user) return false;

        $encoder = $this->encoderFactory->getEncoder($user);

        $loginSuccess = ($encoder->isPasswordValid($user->getPassword(), $plainPassword, $user->getSalt())) ? true : false;

        if ($loginSuccess == false) return false;

        $user->setLastLoginType(User::LOGIN_TYPE['email']);

        return $this->login($user);
    }

    public function registerEmail($email, $password) {
        /** @var User $user */
        $user = $this->userManager->findUserByEmail($email);
        if ($user) return false;

        $user = $this->createUser($email, null, $password, $email);
        if (!$user) return false;

        $user->setLastLoginType(User::LOGIN_TYPE['email']);

        return $this->login($user);

    }

    /**
     * @param $email
     * @param \Swift_Mailer $mailer
     * @param $templating
     * @return bool
     */
    public function sendResetEmailRequest($email, $mailer, $templating) {
        /** @var User $user */
        $user = $this->userManager->findUserByEmail($email);
        if (!$user) return false;

        $baseUrl = 'yipiao://preset?t=';
        $user->setPasswordResetToken(sha1($user->getUsernameCanonical() . time()));
        $user->setPasswordResetTokenExpiresAt(date_add(new \DateTime(), date_interval_create_from_date_string('1 hour')));
        $this->userManager->updateUser($user);

        $message = (new \Swift_Message('Here is a link to reset your password.'))
            ->setFrom('webmaster@yipiao.me')
            ->setTo($user->getEmail())
            ->setBody(
                $templating->render(
                // app/Resources/views/Emails/registration.html.twig
                    'email/password_reset.html.twig',
                    array(
                        'name' => $user->getNickname(),
                        'link' => $baseUrl . $user->getPasswordResetToken(),
                        'admin_email' => $this->adminEmail
                        )
                ),
                'text/html'
            )
        ;

        $mailer->send($message);
        return true;
    }

    public function changePassword($token, $newPassword) {
        if (!$token || !$newPassword) return false;

        /** @var User $user */
        $user = $this->getUserByResetToken($token);
        if (!$user) return false;

        if ($user->getPasswordResetTokenExpiresAt() == null || $user->getPasswordResetTokenExpiresAt() < new \DateTime()) return false;

        $user->setPlainPassword($newPassword);
        $user->setPasswordResetTokenExpiresAt(null);
        $user->setPasswordResetToken(null);
        $this->userManager->updateUser($user);

        $user->setLastLoginType(User::LOGIN_TYPE['passwordReset']);

        return $this->login($user);
    }

    public function verifyResetToken($token) {
        return $this->getUserByResetToken($token) ? true : false;
    }

    private function getUserByResetToken($token) {
        return $this->userManager->findUserBy(['passwordResetToken' => $token]);
    }

    /**
     * @param User $user
     * @return mixed
     */
    private function login($user) {
        if (!$user->isEnabled()) return false;

        //todo token remains active forever. Maybe set Oauth server.
        $user->setAccessToken($this->randomStringGenerator->getGUID());
        $user->setLastLogin(new \DateTime());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user->getAccessToken();
    }

    private function createUser($userName, $weixinAvatar = null, $plainPassword = null, $email = null) {
        /** @var User $user */
        $uniqueUsername = $userName . $this->randomStringGenerator->generate(4);
        $user = $this->userManager->createUser();
        $user->setUsername($uniqueUsername);
        $user->setUsernameCanonical($uniqueUsername);
        $user->setNickname($userName);
        $user->setEmail(!empty($email) ? $email : $this->randomStringGenerator->generate(8)."@yipiao.com");
        $user->setEnabled(true);
        $user->setPlainPassword(!empty($plainPassword) ? $plainPassword : $this->randomStringGenerator->generate(16));
        $user->setRoles([User::ROLE_DEFAULT]);
        if (!empty($weixinAvatar))
            $user->setAvatar($this->uploadAvatar($weixinAvatar));
        $this->userManager->updateUser($user);

        //todo check if this query is necessary
        return $this->userRepository->findOneBy(['id' => $user->getId()]);
    }

    private function createWeixinProfile($user, $weixinJson) {
        /** @var WeixinProfile $weixinProfile */
        $weixinProfile = new WeixinProfile();
        $weixinProfile->setOpenId($weixinJson->openid);
        $weixinProfile->setUnionId($weixinJson->openid);
        $weixinProfile->setData(json_encode($weixinJson));
        $weixinProfile->setUser($user);
        $this->entityManager->persist($weixinProfile);
        $this->entityManager->flush();
        return $weixinProfile;
    }

    private function uploadAvatar($avatar) {

        $fileName = '/tmp/' . $this->randomStringGenerator->generate();
        $downloadedAvatar = file_get_contents($avatar);
        $fp = fopen($fileName, "w");
        fwrite($fp, $downloadedAvatar);
        fclose($fp);
        $downloadedAvatar = new UploadedFile($fileName, $fileName, mime_content_type($fileName), filesize($fileName));
        $uploadResult = $this->qiniuUploader->uploadFile($downloadedAvatar, 'usr', $fileName);
        unlink($fileName);
        return $uploadResult['fileName'];
    }
}