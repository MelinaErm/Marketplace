<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

use App\Http\Controllers\FavoriteController;

use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
Route::get('/messages', [App\Http\Controllers\MessageController::class, 'index'])->name('messages');
Route::post('/messages/send', [App\Http\Controllers\MessageController::class, 'send'])->name('messages.send');

Route::get('/messages/{receiverId}', [MessageController::class, 'getMessagesForReceiver']);

Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
Route::put('/update', [ProfileController::class, 'update'])->name('update');
Route::delete('/delete', [ProfileController::class, 'destroy'])->name('delete');

Route::post('/products', [App\Http\Controllers\ProductController::class, 'store'])->name('products.store');
Route::get('/products/{product}', [App\Http\Controllers\ProductController::class, 'show'])->name('products.show');

Route::get('/home', [App\Http\Controllers\ProductController::class, 'index'])->name('home');

Route::get('/products/{product}/edit', [App\Http\Controllers\ProductController::class, 'edit'])->name('products.edit');
Route::put('/products/{product}', [App\Http\Controllers\ProductController::class, 'update'])->name('products.update');
Route::delete('/products/{product}', [App\Http\Controllers\ProductController::class, 'destroy'])->name('products.destroy');

Route::get('/user/profile/{id}', [App\Http\Controllers\UserProfileController::class, 'show'])->name('user.profile');

//password reset routes
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/unread-messages-count', [MessageController::class, 'getUnreadMessagesCount'])->name('unread-messages-count');
Route::put('/messages/{id}/mark-as-read', [MessageController::class, 'markAsRead'])->name('messages.mark-as-read');


Route::match(['get', 'put'], '/messages/{id}/unread-count', [MessageController::class, 'count'])->name('messages.count');



Route::delete('/messages/{id}', [MessageController::class, 'deleteMessage'])->name('messages.delete');

Route::post('/favorites/{product}', [FavoriteController::class, 'store'])->name('favorites.store');
Route::delete('/favorites/{product}', [FavoriteController::class, 'delete'])->name('favorites.delete');



Route::post('/trigger-new-message-event', [MessageController::class, 'triggerNewMessageEvent'])->name('trigger.new-message-event');



Route::group(['middleware' => ['auth', 'admin']], function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});



