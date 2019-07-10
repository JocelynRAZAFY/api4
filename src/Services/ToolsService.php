<?php
/**
 * Created by PhpStorm.
 * User: jocelyn
 * Date: 5/1/19
 * Time: 9:31 AM
 */

namespace App\Services;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class ToolsService
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var KernelInterface
     */
    private $kernel;

    private $targetDirectory;

    public function __construct(
        ContainerInterface $container,
        KernelInterface $kernel,
        $targetDirectory
    )
    {
        $this->container = $container;
        $this->kernel = $kernel;
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * Get a user from the Security Token Storage.
     *
     * @return mixed
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @see TokenInterface::getUser()
     *
     * @final
     */
    public function getUser()
    {
        if (!$this->container->has('security.token_storage')) {
            throw new \LogicException('The SecurityBundle is not registered in your application. Try running "composer require symfony/security-bundle".');
        }

        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return;
        }

        return $user;
    }

    /**
     * @param $base64String
     * @return string
     */
    public function uploadBase64($base64String)
    {
        $ext = explode('/',explode(';',$base64String)[0])[1];
        $image = explode(',',$base64String)[1];
        $fileName = md5(time().uniqid()).'.'.$ext;

//        $filePath = $this->kernel->getProjectDir().'/public/img/post/'.$fileName;
        $filePath = $this->targetDirectory.'/'.$fileName;
        $data = base64_decode($image);
        file_put_contents($filePath, $data);

        return $fileName;
    }

    /**
     * @param $photo
     * @return string
     */
    public function imageToBase64($photo)
    {
        if($photo != ''){
            $urlImage = $this->targetDirectory.'/'.$photo;
            $type = pathinfo($urlImage, PATHINFO_EXTENSION);
            $data = file_get_contents($urlImage);

            return  'data:image/' . $type . ';base64,' . base64_encode($data);
        }

    }

    public function removeImage($image)
    {
        $urlImage = $this->targetDirectory.'/'.$image;
        if(file_exists($urlImage)){
            unlink($urlImage);
        }
    }


    /**
     * Public function generate uuid v4
     *
     * @return string
     */
    public static function generateUUIDV4()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * @param $data
     * @return array
     */
    public function getDataFormat($data){

        return [
            'links' => [
                'pagination' => [
                    'total' => 50,
                    'per_page' => 3,
                    'current_page' => 1,
                    'last_page' => 2,
                    'next_page_url' => '...',
                    'prev_page_url' => '...',
                    'from' => 1,
                    'to' => 3,
                ]
            ],
            'data' => $data
        ];
    }
}