<?php

use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AdminHomeController;
use App\Http\Controllers\SetupIntentController;
use App\Http\Controllers\PaymentIntentController;
use App\Http\Controllers\CheckoutSubscriptionController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentMethodCheckoutController;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;

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

###################################### Single Charge ######################################

// Route::get('/', function () {
//     $courses = Course::all();
//     return view('home', compact('courses'));
// })->name('home');

Route::controller(CourseController::class)->group(function () {
    Route::get('/courses/{course:slug}', 'show')->name('courses.show');
});

// Cart Management
Route::controller(CartController::class)->group(function () {
    Route::get('/cart', 'index')->name('cart.index');
    Route::get('/addToCart/{course:slug}', 'addtoCart')->name('addtoCart');
    Route::get('/removeFromCart/{course:slug}', 'removeFromCart')->name('removeFromCart');
});

Route::controller(CheckoutController::class)->group(function () {
    Route::get('/checkout', 'checkout')->name('checkout')->middleware('auth');
    Route::get('/checkout/enableCoupons', 'enableCoupons')->name('checkout.enableCoupons')->middleware('auth');
    Route::get('/checkout/nonStripeProduct', 'nonStripeProduct')->name('checkout.nonStripeProduct')->middleware('auth');
    Route::get('/checkout/lineItems', 'lineItems')->name('checkout.lineItems')->middleware('auth');
    Route::get('/checkout/success', 'success')->name('checkout.success')->middleware('auth');
    Route::get('/checkout/cancel', 'cancel')->name('checkout.cancel')->middleware('auth');
});

//Direct Integration - Payment Method
Route::controller(PaymentMethodCheckoutController::class)->group(function () {
    Route::get('/direct/paymentMethod', 'index')->name('direct.paymentMethod')->middleware('auth');
    Route::post('/direct/paymentMethod/post', 'post')->name('direct.paymentMethod.post')->middleware('auth');
    Route::get('/direct/paymentMethod/oneClick', 'oneClick')->name('direct.paymentMethod.oneClick')->middleware(['auth', 'oneClick']);
});

//Direct Integration - Payment Intent
Route::controller(PaymentIntentController::class)->group(function () {
    Route::get('/direct/paymentIntent', 'index')->name('direct.paymentIntent')->middleware('auth');
    Route::post('/direct/paymentIntent/post', 'post')->name('direct.paymentIntent.post')->middleware('auth');
});

//Direct Integration - Setup Intent
Route::controller(SetupIntentController::class)->group(function () {
    Route::get('/direct/setupIntent', 'index')->name('direct.setupIntent')->middleware('auth');
    Route::post('/direct/setupIntent/post', 'post')->name('direct.setupIntent.post')->middleware('auth');
});


// Route::post('stripe/webhook', [WebhookController::class, 'handleWebhook'])->name('cashier.webhook');

################################################ End Of Single Charge    ###########################################################




################################ Subscriptions ################################
Route::get('/plans', [PlanController::class, 'index'])->name('plans');

// Stripe Hosted Checkout
Route::get('/checkout/subscriptions/{plan:slug}', [CheckoutSubscriptionController::class, 'index'])->middleware('auth')->name('checkout.subscription');

// Direct Integration - Payment Method
Route::get('/checkout/direct/subscriptions/{plan:slug}', [CheckoutSubscriptionController::class, 'indexDirect'])->middleware('auth')->name('direct.subscription');
Route::post('/checkout/subscriptions/post', [CheckoutSubscriptionController::class, 'post'])->middleware('auth')->name('direct.subscription.post');


// subscription status & trail
Route::get('/members', function () {
    return view('members');
})->name('members')->middleware('onlySubscribed');

// Route::get('/billing-portal', function () {
//     dd('hello');
// })->middleware('auth')->name('billing-portal');

Route::get('/billing-portal', function () {
    $user = Auth::user();

    // Check if the user has a Stripe customer ID
    if (!$user->hasStripeId()) {
        $user->createAsStripeCustomer();
    }

    // Redirect to the Stripe Billing Portal
    return $user->redirectToBillingPortal(route('plans'));
})->name('billing-portal');


Route::get('/cancel', function () {
    Auth::user()->subscription('monthly-plan')->cancel();
    return back();
})->middleware('auth')->name('cancel');

Route::get('/cancel-now', function () {
    Auth::user()->subscription('monthly-plan')->cancelNow();
    return back();
})->middleware('auth')->name('cancel.now');


Route::get('/resume', function () {
    Auth::user()->subscription('monthly-plan')->resume();
    return back();
})->middleware('auth')->name('resume');


Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices');
Route::get('/invoices/download/{invoiceID}', [InvoiceController::class, 'download'])->name('download');

Route::get('/', function (Request $request) {
    $courses = Course::all();

    if ($request->has('session_id')) {

        $session = Cashier::stripe()->checkout->sessions->retrieve($request->get('session_id'));

        if ($session && $session->status == 'complete') {
            Auth::user()->update(['lifetime_membership' => true]);
        }
    }

    return view('home', compact('courses'));
})->name('home');


##################################################################################

// Route::prefix('admin')->name('admin')->group(function () {
//     Route::middleware('admin')->group(function () {
//         #####------------------------------####
//         Route::get('/', AdminHomeController::class)->name('index');
//     });
//     require __DIR__ . '/adminAuth.php';
// });

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
