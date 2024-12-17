<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Cast\CastTagCategoryController;
use App\Http\Controllers\API\Customer\CustomerCategoryListController;
use App\Http\Controllers\API\Customer\CustomerController;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\Payment\V1\CardController;
use App\Http\Controllers\API\Payment\V1\PaymentController;
use App\Http\Controllers\API\Payment\V1\StripeProductController;
use App\Http\Controllers\API\Payment\V1\SubscriptionController;
use App\Http\Controllers\API\Payment\V1\WebHookController;
use App\Http\Controllers\API\PointHistoryController;
use App\Http\Controllers\API\Order\OrderController;
use App\Http\Controllers\API\Order\RequestMatchingController;
use App\Http\Controllers\API\PushSubscriptionController;
use App\Http\Controllers\API\Tencent\TencentController;
use App\Http\Controllers\GPS\LocationController;
use App\Http\Controllers\SiteAdmin\AccountController;
use App\Http\Controllers\API\Cast\CastCategoryController;
use App\Http\Controllers\API\Cast\CastController;
use App\Http\Controllers\SiteAdmin\UserController;
use App\Http\Controllers\StatusController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('refresh', [AuthController::class, 'refresh']);

Route::post('send-forgot-password-email', [AuthController::class, 'sendEmail']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);
Route::post('/stripe/webhook', [PaymentController::class, 'webhook']);
Route::post('/stripe/subscription-webhook', [WebHookController::class, 'handleWebhook']);

// Protected Routes
Route::middleware(['check.token.version'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('logout-all', [AuthController::class, 'logoutAllDevices']);

    Route::get('profile', [AuthController::class, 'getProfile']);

    //Customer Category
    Route::apiResource('customer-categories', CustomerCategoryListController::class);

    //Customer
    Route::apiResource('customers', CustomerController::class);
    Route::get('customer/home-page', [CustomerController::class, 'homePage']);

    Route::get('customer/get-fav-casts', [CustomerController::class, 'getFavCasts']);
    Route::post('customer/add-fav-cast', [CustomerController::class, 'addFavCast']);
    Route::post('customer/remove-fav-cast', [CustomerController::class, 'removeFavCast']);


    //Cast Category
    Route::apiResource('cast-categories', CastCategoryController::class);

    //Cast
    Route::get('cast/aggregate-orders-monthly', [CastController::class, 'aggregateOrdersMonthly']);
    Route::get('cast/landing/page', [CastController::class, 'homePage']);
    Route::apiResource('cast', CastController::class);


    //Status
    Route::apiResource('statuses', StatusController::class);

    //Image
    Route::post('image/store', [ImageController::class, 'store']);
    Route::post('image/make-profile', [ImageController::class, 'makeProfilePicture']);
    Route::delete('image/{id}', [ImageController::class, 'destroy']);

    //Order
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/customer-test', [OrderController::class, 'getOrdersWithCustomer']);
    Route::post('/orders/direct-call-cast', [OrderController::class, 'directCallCast']);
    Route::post('/orders/leave-order', [OrderController::class, 'leaveOrder']);
    Route::post('/orders/start-order', [OrderController::class, 'startOrder']);
    Route::post('/orders/accept-deet', [OrderController::class, 'acceptDeet']);
    Route::post('/orders/reject-deet', [OrderController::class, 'rejectDeet']);
    Route::post('/orders/save-admin-memo', [OrderController::class, 'saveAdminMemo']);
    Route::post('/orders/save-cast-memo', [OrderController::class, 'saveCastMemo']);

    //Request Matching
    Route::get('/request-matching', [RequestMatchingController::class, 'index']);
    Route::get('/request-matching-designated', [RequestMatchingController::class, 'getDesignatedRequest']);
    Route::post('/request-matching/create', [RequestMatchingController::class, 'requestMatchingOrder']);
    Route::post('/request-matching/detail', [RequestMatchingController::class, 'requestMatchingDetail']);
    Route::post('/request-matching/detail/update', [RequestMatchingController::class, 'updateStatus']);
    Route::post('/request-matching/detail/cancel', [RequestMatchingController::class, 'cancelOrder']);
    Route::post('/request-matching/detail/confirm', [RequestMatchingController::class, 'confirmOrder']);
    Route::post('/request-matching/detail/cast-confirm', [RequestMatchingController::class, 'castConfirm']);

    //PointHistory
    Route::get('/point-history', [PointHistoryController::class, 'index']);
    Route::post('point-history/create-with-holded-point', [PointHistoryController::class, 'storeWithHoldedPoint']);

    Route::post('tencent/chat/create-user', [TencentController::class, 'createUser']);
    Route::post('tencent/chat/update-user', [TencentController::class, 'updateUser']);
    Route::post('tencent/chat/new-token', [TencentController::class, 'generateNewToken']);
    Route::post('tencent/chat/send-message-to-user', [TencentController::class, 'sendMessageToUser']);


    Route::post('/payments/create', [PaymentController::class, 'createPaymentIntent']);
    Route::get('/payments/amount', [PaymentController::class, 'getPaymentAmounts']);
    Route::post('customer/auto-pay', [PaymentController::class, 'autoPay']);



    //Web Push

    Route::post('/notifications/subscribe', [PushSubscriptionController::class, 'subscribe']);
    Route::post('/notifications/unsubscribe', [PushSubscriptionController::class, 'unsubscribe']);
    Route::post('/notifications/broadcast', [PushSubscriptionController::class, 'broadcast']);

    Route::prefix('cards')->group(function () {
        Route::post('/save', [CardController::class, 'save']);
        Route::post('/make-default', [CardController::class, 'makeDefaultCard']);
        Route::get('/{customerId}', [CardController::class, 'get']);
        Route::delete('/delete/{customerId}/{paymentMethodId}', [CardController::class, 'delete']);
    });

    //Subscription Product
    Route::prefix('subscriptions')->group(function () {
        Route::get('/stripe-subscription-products', [StripeProductController::class, 'getActiveProducts']);
        Route::post('/create', [SubscriptionController::class, 'createSubscription']);
        Route::get('/{id}', [SubscriptionController::class, 'fetchSubscription']);
        Route::get('/user/{userId}', [SubscriptionController::class, 'fetchUserSubscriptions']);
    });

        Route::get('/cast-tags', [CastTagCategoryController::class, 'index']);
        Route::post('/cast-tag', [CastTagCategoryController::class, 'store']);
        Route::put('/cast-tag/{id}', [CastTagCategoryController::class, 'update']);
        Route::delete('/cast-tag/{id}', [CastTagCategoryController::class, 'delete']);
        Route::get('/cast-tag/{id}', [CastTagCategoryController::class, 'getTag']);
});

// Webhook route

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('verify.jwt.token')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    // GPS routes
    Route::get('/gps', [LocationController::class, 'index']);
    Route::post('gps/store', [LocationController::class, 'store']);

    Route::middleware('check.token.version')->group(function () {
        Route::post('/siteadmin/account_register', [AccountController::class, 'register']);
        Route::get('/siteadmin/account_settings/', [AccountController::class, 'accountSettings']);
        Route::post('/siteadmin/account_settings/update', [AccountController::class, 'updateAccount']);

        Route::get('/siteadmin/user_register', [UserController::class, 'getAccountInfoForUserRegistration']);
        Route::post('/siteadmin/user_register/action', [UserController::class, 'registerUser']);
        Route::get('/siteadmin/user_settings', [UserController::class, 'getUserInfoForUserSettings']);
        Route::post('/siteadmin/user_settings/update', [UserController::class, 'updateUserSettings']);

        Route::get('/siteadmin/user_list', [UserController::class, 'getUserLists']);
        Route::post('/siteadmin/activate_user', [UserController::class, 'activateUser']);

        //Order
        Route::get('/siteadmin/orders', [OrderController::class, 'index']);

//        Route::post('/siteadmin/cast_tag_register', [CastController::class, 'registerSpecTag']);
//        Route::get('/siteadmin/cast_spec_tags', [CastController::class, 'castSpecTagList']);
//        Route::post('/siteadmin/cast_spec_tag/update', [CastController::class, 'castSpecTagUpdate']);
//        Route::post('/siteadmin/cast_spec_tag/delete', [CastController::class, 'castSpecTagDelete']);
//        Route::get('/siteadmin/cast_spec_tag/{id}', [CastController::class, 'getSingleSpecTag']);

//        Route::post('/siteadmin/cast_category_register', [CastController::class, 'registerCastCategory']);
//        Route::get('/siteadmin/cast_categories', [CastController::class, 'castCategoryList']);
//        Route::post('/siteadmin/cast_category/update', [CastController::class, 'castCategoryUpdate']);
//        Route::post('/siteadmin/cast_category/delete', [CastController::class, 'castCategoryDelete']);
//        Route::get('/siteadmin/cast_category/{id}', [CastController::class, 'getSingleCastCategory']);

        // Cast routes
//        Route::post('/siteadmin/cast_register', [CastController::class, 'store']);
//        Route::post('/siteadmin/cast_search', [CastController::class, 'castSearch']);
//        Route::get('/siteadmin/cast_referral_search', [CastController::class, 'castReferralSearch']);

        // Customer register routes
//        Route::post('/siteadmin/customer_register/', [CustomerController::class, 'store']);
//
//        // Customer category routes
//        Route::post('/siteadmin/customer_category_register', [CustomerController::class, 'registerCustomerCategory']);
//        Route::post('/siteadmin/customer_search', [CustomerController::class, 'customerSearch']);
//        Route::get('/siteadmin/customer_category_search/{id}', [CustomerController::class, 'customer_category_search']);
//        Route::post('/siteadmin/customer_category_update', [CustomerController::class, 'customer_category_update']);
//        Route::delete('/siteadmin/customer_category_delete/{id}', [CustomerController::class, 'customer_category_delete']);
    });

    //image related
//    Route::post('/siteadmin/image_upload', [ImageController::class, 'store']);
//    Route::post('/siteadmin/image_delete/{id}', [ImageController::class, 'destroy']);

});
