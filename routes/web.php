<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\User\AboutController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\user\CartController;
use App\Http\Controllers\User\ShopController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\User\RatingController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\User\TransactionController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\User\ProductController as UserProductController;

// ================== HOME ================== //
Route::get('/', fn() => view('hero'))->name('hero');

// ================== AUTH ================== //
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ================== PUBLIC PAGES ================== //
Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/details/{id}', [ProductController::class, 'show'])->name('user.details');
Route::get('/about', [AboutController::class, 'index'])->name('about');

// ================== USER (Auth Required) ================== //
Route::prefix('user')->name('user.')->middleware('auth')->group(function () {
    // Show profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
    Route::get('/profile/orders', [ProfileController::class, 'orders'])->name('orders');
     // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart'); // show cart
    Route::get('/cart/data', [CartController::class, 'data'])->name('cart.data');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add'); // add to cart
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove'); // remove single item
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear'); // clear cart
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
        // Chat
        Route::get('/chat', [ChatController::class, 'indexUser'])->name('chat.index');
    Route::get('/chat/{user}', [ChatController::class, 'showUser'])->name('chat.show');
    Route::post('/chat/{user}/send', [ChatController::class, 'sendUser'])->name('chat.send');


// Transaction routes
 // Transaction page (from cart or single product)
   Route::get('/transaction', [TransactionController::class, 'index'])->name('transaction'); 
Route::post('/transaction/buy', [TransactionController::class, 'buy'])->name('transaction.buy');
Route::post('/transaction/finalize', [TransactionController::class, 'finalize'])->name('transaction.finalize');
Route::get('/transaction/{id}', [TransactionController::class, 'show'])->name('transaction.show');
    Route::get('/transaction/{id}/invoice', [TransactionController::class, 'invoice'])->name('transaction.invoice');

     // Ratings
    Route::get('/rating', [RatingController::class, 'index'])->name('rating');
    Route::post('/rating', [RatingController::class, 'store'])->name('rating.store');
});

// ================== ADMIN ================== //
// Admin login/logout
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Admin-only pages
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('dashboard');

     Route::get('/category', [CategoryController::class, 'index'])->name('category');
    Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
    Route::put('/category/{category}', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('/category/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');

    Route::get('/order', [OrderController::class, 'index'])->name('order'); 
    Route::post('/order/{order}/status', [OrderController::class, 'updateStatus'])
    ->name('admin.orders.updateStatus');
    Route::get('/order/{order}/details', [OrderController::class, 'details'])->name('order.details'); 
    Route::get('/order/{order}/summary', [OrderController::class, 'summary']);


    //notif
   Route::get('/navbar/notifications', [NotificationController::class, 'fetch'])
        ->name('adminnavbar.notifications');
    Route::post('/navbar/notifications/read', [NotificationController::class, 'markAsRead'])
        ->name('adminnavbar.notifications.markAsRead');

    //chat
    Route::get('/chat', [ChatController::class, 'indexAdmin'])->name('chat.index');
    Route::get('/chat/{user}', [ChatController::class, 'showAdmin'])->name('chat.show');
    Route::post('/chat/{user}/send', [ChatController::class, 'sendAdmin'])->name('chat.send');

    // Promotions CRUD
    Route::get('/promotion', [PromotionController::class, 'index'])->name('promotion');
    Route::post('/promotion', [PromotionController::class, 'store'])->name('promotion.store');
    Route::put('/promotion/{promotion}', [PromotionController::class, 'update'])->name('promotion.update');
    Route::delete('/promotion/{promotion}', [PromotionController::class, 'destroy'])->name('promotion.destroy');

// Profile page (GET)
    Route::get('/profile', [AdminProfileController::class, 'profile'])
        ->name('adminprofile');

    // Profile update (PUT for inline editing, POST for avatar upload)
    Route::match(['put', 'post'], '/profile/update', [AdminProfileController::class, 'profile'])
        ->name('profile.update');

    // Products CRUD
    Route::resource('products', ProductController::class)->except(['show']);
});
