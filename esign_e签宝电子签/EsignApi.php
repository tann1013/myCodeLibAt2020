<?php

/**
 * E签宝电子签名
 * @author tann1013@hotmail.com
 * @date 2019年3月25日
 * @version 1.0
 * @base e签宝电子签名服务-原文签名-无流程版对接手册v2.4.11-标准版
 *
 */
namespace App\Services\Esign;

use Illuminate\Support\Facades\Log;

class EsignApi
{
    //基础配置
    const CURL_TIMEOUT = 60;
    const SUCCESS_CODE = 0;
    const ERR_MSG = 'EsignApi:';
    const MISSING_PARAMS_CODE = 301;

    public $base_url = 'https://smlo.tsign.cn/opentreaty-service';
    public $requestHeader = array();
    public $config = array();
    public $validator = '';

    public function __construct()
    {
        //验证器
        $this->validator = new EsignValidator();
    }

    /**
     * 1、创建个人账户
     * @param $input
     */
    public function accountCreatePerson($input){
        $method = '/account/create/person';
        $params = [
            'thirdId'=> isset($input['thirdId'])? $input['thirdId']: '',
            'name'   => isset($input['name'])? $input['name']: '',
            'idNo'   => isset($input['idNo'])? $input['idNo']: '',
            'idType' => isset($input['idType'])? $input['idType']: '',
            'mobile' => isset($input['mobile'])? $input['mobile']: '',
            'email'  => isset($input['email'])? $input['email']: '',
        ];
        //thirdId String 否 个人用户在对接方的账号标识字段，开发者可用该字段来唯一标识一个个人用户，用于与e签宝的个人账号做映射，相同 thirdId 不可重复使用。最大支持50 个字节
        //name String 是 姓名
        //idNo String 是 证件号
        //idType int 是 详见个人账号证件类型
        //mobile String 否 手机号
        //email String 否 邮箱
        /**
         * 1、验参
         */
        $validator = $this->validator->accountCreatePersonValidate($input);
        if ($validator->fails()) {
            return array( 'errCode' => 301, 'msg' => $validator->errors()->first());
        }
        /**
         * 2、请求
         */
        $response = $this->_curlPost($method , $params);
        /**
         * 3、异常处理
         */
        if($response['errCode'] != self::SUCCESS_CODE){
        }
        return $response;
    }

    /**
     * 2、创建企业账号
     * @param $input
     */
    public function accountCreateOrganizeCommon($input){
        $method = '/account/create/organize/common';
        $params = [
            'creatorId'   => isset($input['creatorId'])? $input['creatorId']: '',//creatorId String 是
            'thirdId'     => isset($input['thirdId'])? $input['thirdId']: '',//thirdId String 否
            'name'        => isset($input['name'])? $input['name']: '',//是 企业名称
            'organCode'   => isset($input['organCode'])? $input['organCode']: '',// 是 企业证照号
            'organType'   => isset($input['organType'])? $input['organType']: '',//是 详见企业账号证照类型
            'legalName'   => isset($input['legalName'])? $input['legalName']: '',// 否 企业法人姓名
            'legalIdNo'   => isset($input['legalIdNo'])? $input['legalIdNo']: '',//否 企业法人证件号
            'legalIdType' => isset($input['legalIdType'])? $input['legalIdType']: '',// 否 详见个人账号证件类型 当证件号字段 legalIdNo 填写时，该字段必填
            'email'       => isset($input['email'])? $input['email']: '',// 否 企业邮箱
        ];
        /**
         * 1、验参
         */
        $validator = $this->validator->accountCreateOrganizeCommonValidate($input);
        if ($validator->fails()) {
            return array( 'errCode' => 301, 'msg' => $validator->errors()->first());
        }
        /**
         * 2、请求
         */
        $response = $this->_curlPost($method , $params);
        /**
         * 3、异常处理
         */
        if($response['errCode'] != self::SUCCESS_CODE){
            Log::error(self::ERR_MSG.$response['msg'].'#'.$response['errCode']);
        }
        return $response;
    }

    /**
     * 3、本地文件创建合同#/doc/createbyfilekey
     * @param $input
     */
    public function docCreatebyFilekey($input){
        $method = '/doc/createbyfilekey';
        $params = [
            'fileKey' => isset($input['fileKey'])? $input['fileKey']: '',
        ];
        /**
         * 验参
         */
        $validator = $this->validator->docCreatebyFilekeyValidate($input);
        if ($validator->fails()) {
            return array( 'errCode' => 301, 'msg' => $validator->errors()->first());
        }
        /**
         * 请求
         */
        $response = $this->_curlPost($method , $params);
        /**
         * 异常处理
         */
        if($response['errCode'] != self::SUCCESS_CODE){
            Log::error(self::ERR_MSG.$response['msg'].'#'.$response['errCode']);
        }
        return $response;
    }

    /**
     * 4、合同模板创建合同#/doc/createbytemplate
     * @param $input
     */
    public function docCreatebyTemplate($input){
        $method = '/doc/createbytemplate';
        $params = [
            'name' => isset($input['name'])? $input['name']: '',
            'templateId' => isset($input['templateId'])? $input['templateId']: '',
            'simpleFormFields' => isset($input['fileKey'])? $input['fileKey']: [],
            'chunkedFields' => isset($input['fileKey'])? $input['fileKey']: [],
        ];
        /**
         * 1、验参
         */
        $validator = $this->validator->docCreatebyTemplateValidate($input);
        if ($validator->fails()) {
            return array( 'errCode' => 301, 'msg' => $validator->errors()->first());
        }
        /**
         * 2、请求
         */
        $response = $this->_curlPost($method , $params);
        /**
         * 3、异常处理
         */
        if($response['errCode'] != self::SUCCESS_CODE){
            Log::error(self::ERR_MSG.$response['msg'].'#'.$response['errCode']);
        }
        return $response;
    }

    /**
     * 5、创建合同签署流程#/sign/contract/addProcess
     * @param $input
     */
    public function signContractAddProcess($input){
        $method = '/sign/contract/addProcess';
        $params = [
            'businessScene' => isset($input['businessScene'])? $input['businessScene']: '',
            'docId' => isset($input['docId'])? $input['docId']: '',
            'initiatorAccountId' => isset($input['initiatorAccountId'])? $input['initiatorAccountId']: '',
            'contractValidity' => isset($input['contractValidity'])? $input['contractValidity']: '',
            'payer' =>isset($input['payer'])? $input['payer']: '',
            'signPlatform' => isset($input['signPlatform'])? $input['signPlatform']: '',
            'noticeType' => isset($input['noticeType'])? $input['noticeType']: '',
            'redirectUrl' => isset($input['redirectUrl'])? $input['redirectUrl']: '',
        ];
        /**
         * 1、验参
         */
        $validator = $this->validator->signContractAddProcessValidate($input);
        if ($validator->fails()) {
            return array( 'errCode' => self::MISSING_PARAMS_CODE, 'msg' => $validator->errors()->first());
        }
        /**
         * 2、请求
         */
        $response = $this->_curlPost($method , $params);
        /**
         * 3、异常处理
         */
        if($response['errCode'] != self::SUCCESS_CODE){
            Log::error(self::ERR_MSG.$response['msg'].'#'.$response['errCode']);
        }
        return $response;
    }
    /**
     * 6、归档流程#/sign/contract/archiveProcess
     * @param $input
     */
    public function signContractArchiveProcess($input){
        $method = '/sign/contract/archiveProcess';
        $params = [
            'flowId' => $input['flowId']
        ];
        //flowId String 是 签署流程id，由创建流程接口的调用返回的 flowId
        /**
         * 1、验参
         */
        $validator = $this->validator->signContractArchiveProcessValidate($input);
        if ($validator->fails()) {
            return array( 'errCode' => 301, 'msg' => $validator->errors()->first());
        }
        /**
         * 2、请求
         */
        $response = $this->_curlPost($method , $params);
        /**
         * 3、异常处理
         */
        if($response['errCode'] != self::SUCCESS_CODE){
            Log::error(self::ERR_MSG.$response['msg'].'#'.$response['errCode']);
        }
        return $response;
    }


    /**
     * 获取文件直传地址
     * @param $input
     * @return mixed
     */
    public function fileUploadUrl($input){
        $method = '/file/uploadurl';
        $params = [
            'fileName' =>isset($input['fileName'])? $input['fileName']: '',
            'fileSize' =>isset($input['fileSize'])? $input['fileSize']: '',
            'contentType' =>isset($input['contentType'])? $input['contentType']: '',
            'contentMd5' =>isset($input['contentMd5'])? $input['contentMd5']: '',
        ];
        //fileName String 是 文件名称
        //fileSize long 是 文件大小，单位字节
        //contentType String 是 文件内容的 MIME 类型描述字符串
        //contentMd5 String 是 采用 BASE64 编码的文件 MD5 摘要值
        /**
         * 1、验参
         */
        $validator = $this->validator->fileUploadUrlValidate($input);
        if ($validator->fails()) {
            return array( 'errCode' => 301, 'msg' => $validator->errors()->first());
        }
        /**
         * 2、请求
         */
        $response = $this->_curlPost($method , $params);
        /**
         * 3、异常处理
         */
        if($response['errCode'] != self::SUCCESS_CODE){
            Log::error(self::ERR_MSG.$response['msg'].'#'.$response['errCode']);
        }
        return $response;
    }

    ///template/createbyfilekey
    public function templateCreateByFilekey($input){
        $method = '/file/uploadurl';
        $params = [
            'fileKey' =>isset($input['fileKey'])? $input['fileKey']: '',
            'templateName' =>isset($input['templateName'])? $input['templateName']: '',
            'templateTypeId' =>isset($input['templateTypeId'])? $input['templateTypeId']: '',
            'templateFormKeys' =>isset($input['templateFormKeys'])? $input['templateFormKeys']: '',
        ];
        //fileKey String 是 文件标识，由获取文件直传地址接口返回的 filekey
        //templateName String 是 模板名称
        //templateTypeId String 否 模板创建后所属类别的 Id 预留字段，用于后续扩展支持模板管理功能 当前默认为“0”- 默认类别
        //templateFormKeys
        //List<String> 否 扩展字段，预留模板文件中待填充表单域的 Key 集合 如模板为 PDF 文档，由于文本表单域可直 接在 PDF 文档中解析，故不需要填写； 如模板为非 PDF 文档，则需要指定
        /**
         * 1、验参
         */
        $validator = $this->validator->templateCreateByFilekeyValidate($input);
        if ($validator->fails()) {
            return array( 'errCode' => 301, 'msg' => $validator->errors()->first());
        }
        /**
         * 2、请求
         */
        $response = $this->_curlPost($method , $params);
        /**
         * 3、异常处理
         */
        if($response['errCode'] != self::SUCCESS_CODE){
            Log::error(self::ERR_MSG.$response['msg'].'#'.$response['errCode']);
        }
        return $response;
    }

    /**
     * @param $url
     * @param $params
     * @return mixed
     */
    private function _curlPost($url, $params){
        $url = $this->base_url.$url;
        $postStr = json_encode($params);//转出json字符串
        $config = [
            'appId'     => '4438761035',
            'appSecret' => '7477297e8589352482ae19a55c7c2bc0',
        ];
        $requestHeader = array(
            'X-Tsign-Open-App-Id:' . $config['appId'],
            'X-Tsign-Open-App-Secret:' . $config['appSecret'],
            'Content-Type:' . 'application/json'
        );
        /**
         * curl
         */
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//SSL证书认证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HEADER, false); // 输出HTTP头 true
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        curl_setopt($curl, CURLOPT_POST, true); // post传输数据
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postStr);// post传输数据
        curl_setopt($curl, CURLOPT_HTTPHEADER, $requestHeader);
        $result = curl_exec($curl);
        curl_close($curl);
        //sleep(1);
        return json_decode($result, true);
    }

}