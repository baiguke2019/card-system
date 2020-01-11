<?php
namespace App\Http\Controllers\Shop; use App\Category; use App\Product; use App\Library\Response; use Carbon\Carbon; use Illuminate\Http\Request; use App\Http\Controllers\Controller; class Coupon extends Controller { function info(Request $sp375069) { $spca39ca = (int) $sp375069->post('category_id', -1); $sp138ddb = (int) $sp375069->post('product_id', -1); $sp696fca = $sp375069->post('coupon'); if (!$sp696fca) { return Response::fail('请输入优惠券'); } if ($spca39ca > 0) { $spe4707e = Category::findOrFail($spca39ca); $spc2f05b = $spe4707e->user_id; } elseif ($sp138ddb > 0) { $sp6018c8 = Product::findOrFail($sp138ddb); $spc2f05b = $sp6018c8->user_id; } else { return Response::fail('请先选择分类或商品'); } $spede8d7 = \App\Coupon::where('user_id', $spc2f05b)->where('coupon', $sp696fca)->where('expire_at', '>', Carbon::now())->whereRaw('`count_used`<`count_all`')->get(); foreach ($spede8d7 as $sp696fca) { if ($sp696fca->category_id === -1 || $sp696fca->category_id === $spca39ca && ($sp696fca->product_id === -1 || $sp696fca->product_id === $sp138ddb)) { $sp696fca->setVisible(array('discount_type', 'discount_val')); return Response::success($sp696fca); } } return Response::fail('您输入的优惠券信息无效<br>如果没有优惠券请不要填写'); } }