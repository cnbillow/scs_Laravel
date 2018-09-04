<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//
//Route::get('/', function () {
//    return view('welcome');
//});

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: Content-Type,Access-Token,Authorization");
header("Access-Control-Expose-Headers: *");


Route::get("/",function(){
    return "asd";
});
//登录
Route::post('/login', 'PassportController@login');
//注册
Route::post('/register', 'PassportController@register');
//管理员登录
Route::post("/admin_login","PassportController@admin_login");
//注册验证码发送
Route::post("/register_code","PassportController@get_code");
//-------------------------------------------------------------------------//
//忘记密码 - 发送验证码
Route::post("/reset_code","UserController@reset_code");
//忘记密码 - 记录
Route::post("/reset_mes","UserController@reset_mes");
//忘记密码 - 修改 删除记录
Route::post("/reset_pass","PassportController@reset_pass");
//-------------------------------------------------------------------------//
//获取兼职
Route::get("/job","JobController@index");
//获取类型兼职
Route::post("/job/type","JobController@find_job_type");
//获取对应ID兼职
Route::get("/job/{id}","JobController@show");
//-------------------------------------------------------------------------//
//首页三条最近兼职
Route::get("/job_index", "JobController@job_index");
//-------------------------------------------------------------------------//
//首先显示评价
Route::get("/evaluate_show", "EvaluateController@index_show_evaluate");
//评价详细
Route::get("/evaluate_detail/{id}", "EvaluateController@show");
//登录权限（中间间）
Route::group(['middleware' => 'auth:api'],function(){
    //更改个人兼职状态
    Route::post("/user_job_status",'UserController@job_status');
    //更改头像
    Route::post("/user_img_change","UserController@user_img");
    //更改昵称
    Route::post("/user_nickname_change","UserController@user_nickname");
    //获取用户角色
    Route::get("/user_role", "UserController@user_role");
    //-------------------------------------------------------------------------//
    //提出问题
    Route::post("/customer", "CustomerController@store");
    //我提出的问题
    Route::get("/my-customer", "CustomerController@show");
//-------------------------------------------------------------------------//
    //学生权限（中间件）
    Route::group(['middleware' => 'IsStudent'],function(){
        //获取个人信息
        Route::get('/user', 'UserController@index');
        //插入个人信息
        Route::post('/user_personal','PersonalController@store');
        //删除个人信息（重新填写）
        Route::post('/user_personal_del','PersonalController@destroy');
        //-------------------------------------------------------------------------//
        //报名兼职
        Route::post("/job-sign/{id}","JobController@job_sign");
        //查看自己的兼职
        Route::get("/my-job","JobController@my_job");
        //-------------------------------------------------------------------------//
        //加入兼职权限（中间件）
        Route::group(['middleware' => 'JobSign'],function(){
            //查看自己的兼职的负责人信息
            Route::get("/my-job-leader/{id}","LeaderController@show");
            //发表兼职反馈
            Route::post("/job-feedback/{id}", "JobController@job_feedback_insert");
        });
    });
    //-------------------------------------------------------------------------//
    //企业权限（中间件）
    Route::group(['middleware' => 'IsEnterprise'],function(){
        //获取企业信息
        Route::get("/enterprise", "EnterpriseController@show");
        //插入企业信息
        Route::post("/personal_enterprise", "EnterpriseController@store");
        //删除企业信息（重新填写）
        Route::post("/personal_enterprise_del", "EnterpriseController@destroy");
        //-------------------------------------------------------------------------//
        //添加兼职
        Route::post("/send_job","JobController@job_insert");
        //通过学生报名的兼职
        Route::post("/enterprise/job-sign/adopt/{user_id}/job/{job_id}", "JobController@job_sign_adopt");
        //拒绝学生报名的兼职
        Route::post("/enterprise/job-sign/refuse/{user_id}/job/{job_id}", "JobController@job_sign_refuse");
        //查看自己发布的兼职
        Route::get("/enterprise/job", "JobController@enterprise_my_job");
        //-------------------------------------------------------------------------//
        //只能看自己的兼职信息（中间件）
        Route::group(["middleware" => "Enterprise_job"],function(){
            //查看自己的兼职的负责人信息
            Route::get("/my-job-leader/{id}","LeaderController@show");
            //查看报名学生
            Route::get("/enterprise/job-sign/{id}", "JobController@job_sign_student");
            //条件搜索
            Route::post("/enterprise/job-sign/student/{id}", "AdminController@sign_condition");
            //兼职完结
            Route::get("/enterprise/job/{id}/over", "OverController@over");
            //评价
            Route::post("/enterprise/evaluate/{id}", "EvaluateController@store");
        });
    });
    //管理员（中间件）
    Route::group(['middleware' => 'IsAdmin'],function(){
        //获取审核中用户
        Route::get("/admin/user",'AdminController@index');
        //搜索引擎
        Route::post("/admin/user/search","AdminController@search");
        //条件搜索
        Route::post("/admin/user/condition","AdminController@condition");
        //通过审核
        Route::get("/admin/user/access/{id}","AdminController@access");
        //拒绝审核
        Route::get("/admin/user/refuse/{id}","AdminController@refuse");
//-------------------------------------------------------------------------//
        //获取审核中企业
        Route::get("/admin/enterprise","AdminController@enterprise_index");
        //条件搜索
        Route::post("/admin/enterprise/condition","AdminController@enterprise_condition");
        //通过审核
        Route::get("/admin/enterprise/access/{id}","AdminController@enterprise_access");
        //拒绝审核
        Route::get("/admin/enterprise/refuse/{id}","AdminController@enterprise_refuse");
//-------------------------------------------------------------------------//
        //获取审核中兼职
        Route::get("/admin/job", "AdminController@job_index");
        //通过审核
        Route::post("/admin/job/access/{id}", "AdminController@job_access");
        //拒绝审核
        Route::get("/admin/job/refuse/{id}", "AdminController@job_refuse");
        //条件搜索
        Route::post("/admin/job/condition", "AdminController@job_condition");
//-------------------------------------------------------------------------//
        //领队人
        Route::resource("/admin/leader", "LeaderController");
        //通过用户名查找用户信息
        Route::post("/admin/leader/user", "LeaderController@find_user");
//-------------------------------------------------------------------------//
        //获取所有兼职反馈
        Route::get("/admin/job-feedback", "JobController@job_feedback_all");
//-------------------------------------------------------------------------//
        //客服获取所有问题
        Route::get("/admin/customer", "CustomerController@index");
        //回答问题
        Route::post("/admin/customer-an", "CustomerController@an");
//-------------------------------------------------------------------------//
        //获取所有评价并分类出显示与没显示的
        Route::get("/admin/index_evaluate", "EvaluateController@admin_evaluate_index");
        //更改展示评价数据
        Route::post("/admin/index_evaluate/change", "EvaluateController@change_evaluate_index");
//-------------------------------------------------------------------------//
        //管理员兼职完结（线下收钱后）
        Route::post("/admin/over_job/{id}", "AdminController@admin_job_over");
//-------------------------------------------------------------------------//
        //管理员列表的小数字（未处理）
        Route::get("/admin/treated", "AdminController@admin_treated");
    });
});
