<?php
/**
 * 接口验证器
 * @author tann1013@hotmail.com
 */
namespace App\Services\Esign;


use Illuminate\Support\Facades\Validator;

class EsignValidator
{
    public function accountCreatePersonValidate($input){
        return Validator::make($input, [
            'name' => 'required|string',
            'idNo' => 'required',
            'idType' => 'required|integer',
        ], [
            'required' => ':attribute为必填项',
        ], [
            'name' => '姓名',
            'idNo' => '证件号',
            'idType' => '个人账号证件类型',
        ]);
    }
    public function accountCreateOrganizeCommonValidate($input){
        return Validator::make($input, [
            'creatorId' => 'required',
            'name' => 'required|string',
            'organCode' => 'required',
            'organType' => 'required',
        ], [
            'required' => ':attribute为必填项',
        ], [
            'creatorId'    => '企业创建人',
            'name' => '企业名称',
            'organCode' => '企业证照号',
            'organType' => '企业账号证照类型',
        ]);
    }
    public function docCreatebyFilekeyValidate($input){
        return Validator::make($input, [
            'fileKey' => 'required|string'
        ], [
            'required' => ':attribute为必填项',
        ], [
            'fileKey'    => '文件标识'
        ]);
    }
    public function docCreatebyTemplateValidate($input){
        return Validator::make($input, [
            'templateId' => 'required|string',
            'name' => 'required|string'
        ], [
            'required' => ':attribute为必填项',
        ], [
            'templateId'    => '模板id',
            'name' => '合同名称'
        ]);
    }
    public function signContractAddProcessValidate($input){
        return Validator::make($input, [
            'businessScene' => 'required|string',
            'docId' => 'required|string',
        ], [
            'required' => ':attribute为必填项',
        ], [
            'businessScene' => '业务场景名称',
            'docId' => '合同id',
        ]);
    }
    public function signContractArchiveProcessValidate($input){
        return Validator::make($input, [
            'flowId' => 'required|string',
        ], [
            'required' => ':attribute为必填项',
        ], [
            'flowId' => '签署流程id',
        ]);
    }
    public function fileUploadUrlValidate($input){
        return Validator::make($input, [
            'fileName' => 'required|string',
            'fileSize' => 'required|integer',
            'contentType' => 'required|string',
            'contentMd5' => 'required|string',
        ], [
            'required' => ':attribute为必填项',
        ], [
            'fileName'    => '文件名称',
            'fileSize' => '文件大小(单位字节)',
            'contentType' => '文件内容的MIME类型描述字符串',
            'contentMd5' => '文件MD5摘要值',
        ]);
    }
    public function templateCreateByFilekeyValidate($input){
        return Validator::make($input, [
            'fileKey' => 'required|string',
            'templateName' => 'required|string',
        ], [
            'required' => ':attribute为必填项',
        ], [
            'fileName'    => '文件标识',
            'templateName' => '模板名称',
        ]);
    }
}