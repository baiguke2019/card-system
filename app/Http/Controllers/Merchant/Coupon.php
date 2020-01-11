<?php
namespace App\Http\Controllers\Merchant; use App\Library\Response; use Carbon\Carbon; use Illuminate\Http\Request; use App\Http\Controllers\Controller; class Coupon extends Controller { function get(Request $sp375069) { $sped9569 = $this->authQuery($sp375069, \App\Coupon::class)->with(array('category' => function ($sped9569) { $sped9569->select(array('id', 'name')); }))->with(array('product' => function ($sped9569) { $sped9569->select(array('id', 'name')); })); $spca3671 = $sp375069->input('search', false); $spb689af = $sp375069->input('val', false); if ($spca3671 && $spb689af) { if ($spca3671 == 'id') { $sped9569->where('id', $spb689af); } else { $sped9569->where($spca3671, 'like', '%' . $spb689af . '%'); } } $spca39ca = (int) $sp375069->input('category_id'); $sp138ddb = $sp375069->input('product_id', -1); if ($spca39ca > 0) { if ($sp138ddb > 0) { $sped9569->where('product_id', $sp138ddb); } else { $sped9569->where('category_id', $spca39ca); } } $spfc8a83 = $sp375069->input('status'); if (strlen($spfc8a83)) { $sped9569->whereIn('status', explode(',', $spfc8a83)); } $sp2d4d5b = $sp375069->input('type'); if (strlen($sp2d4d5b)) { $sped9569->whereIn('type', explode(',', $sp2d4d5b)); } $sped9569->orderByRaw('expire_at DESC,category_id,product_id,type,status'); $sp19fe4a = (int) $sp375069->input('current_page', 1); $sp90d207 = (int) $sp375069->input('per_page', 20); $sp3aa6bd = $sped9569->paginate($sp90d207, array('*'), 'page', $sp19fe4a); return Response::success($sp3aa6bd); } function create(Request $sp375069) { $spbefb16 = $sp375069->post('count', 0); $sp2d4d5b = (int) $sp375069->post('type', \App\Coupon::TYPE_ONETIME); $sp92c143 = $sp375069->post('expire_at'); $sp0748b4 = (int) $sp375069->post('discount_val'); $spf50c9c = (int) $sp375069->post('discount_type', \App\Coupon::DISCOUNT_TYPE_AMOUNT); $spcc00c8 = $sp375069->post('remark'); if ($spf50c9c === \App\Coupon::DISCOUNT_TYPE_AMOUNT) { if ($sp0748b4 < 1 || $sp0748b4 > 1000000000) { return Response::fail('优惠券面额需要在0.01-10000000之间'); } } if ($spf50c9c === \App\Coupon::DISCOUNT_TYPE_PERCENT) { if ($sp0748b4 < 1 || $sp0748b4 > 100) { return Response::fail('优惠券面额需要在1-100之间'); } } $spca39ca = (int) $sp375069->post('category_id', -1); $sp138ddb = (int) $sp375069->post('product_id', -1); if ($sp2d4d5b === \App\Coupon::TYPE_REPEAT) { $sp696fca = $sp375069->post('coupon'); if (!$sp696fca) { $sp696fca = strtoupper(str_random()); } $sp5588d7 = new \App\Coupon(); $sp5588d7->user_id = $this->getUserIdOrFail($sp375069); $sp5588d7->category_id = $spca39ca; $sp5588d7->product_id = $sp138ddb; $sp5588d7->coupon = $sp696fca; $sp5588d7->type = $sp2d4d5b; $sp5588d7->discount_val = $sp0748b4; $sp5588d7->discount_type = $spf50c9c; $sp5588d7->count_all = (int) $sp375069->post('count_all', 1); if ($sp5588d7->count_all < 1 || $sp5588d7->count_all > 10000000) { return Response::fail('可用次数不能超过10000000'); } $sp5588d7->expire_at = $sp92c143; $sp5588d7->saveOrFail(); return Response::success(array($sp5588d7->coupon)); } elseif ($sp2d4d5b === \App\Coupon::TYPE_ONETIME) { if (!$spbefb16) { return Response::forbidden('请输入生成数量'); } if ($spbefb16 > 100) { return Response::forbidden('每次生成不能大于100张'); } $spede8d7 = array(); $spac0aed = array(); $spc2f05b = $this->getUserIdOrFail($sp375069); $spf8b4c4 = Carbon::now(); for ($sp9d4bce = 0; $sp9d4bce < $spbefb16; $sp9d4bce++) { $sp5588d7 = strtoupper(str_random()); $spac0aed[] = $sp5588d7; $spede8d7[] = array('user_id' => $spc2f05b, 'coupon' => $sp5588d7, 'category_id' => $spca39ca, 'product_id' => $sp138ddb, 'type' => $sp2d4d5b, 'discount_val' => $sp0748b4, 'discount_type' => $spf50c9c, 'status' => \App\Coupon::STATUS_NORMAL, 'remark' => $spcc00c8, 'created_at' => $spf8b4c4, 'expire_at' => $sp92c143); } \App\Coupon::insert($spede8d7); return Response::success($spac0aed); } else { return Response::forbidden('unknown type: ' . $sp2d4d5b); } } function edit(Request $sp375069) { $sp39113c = (int) $sp375069->post('id'); $sp696fca = $sp375069->post('coupon'); $spca39ca = (int) $sp375069->post('category_id', -1); $sp138ddb = (int) $sp375069->post('product_id', -1); $sp92c143 = $sp375069->post('expire_at', NULL); $spfc8a83 = (int) $sp375069->post('status', \App\Coupon::STATUS_NORMAL); $sp2d4d5b = (int) $sp375069->post('type', \App\Coupon::TYPE_ONETIME); $sp0748b4 = (int) $sp375069->post('discount_val'); $spf50c9c = (int) $sp375069->post('discount_type', \App\Coupon::DISCOUNT_TYPE_AMOUNT); if ($spf50c9c === \App\Coupon::DISCOUNT_TYPE_AMOUNT) { if ($sp0748b4 < 1 || $sp0748b4 > 1000000000) { return Response::fail('优惠券面额需要在0.01-10000000之间'); } } if ($spf50c9c === \App\Coupon::DISCOUNT_TYPE_PERCENT) { if ($sp0748b4 < 1 || $sp0748b4 > 100) { return Response::fail('优惠券面额需要在1-100之间'); } } $sp5588d7 = $this->authQuery($sp375069, \App\Coupon::class)->find($sp39113c); if ($sp5588d7) { $sp5588d7->coupon = $sp696fca; $sp5588d7->category_id = $spca39ca; $sp5588d7->product_id = $sp138ddb; $sp5588d7->status = $spfc8a83; $sp5588d7->type = $sp2d4d5b; $sp5588d7->discount_val = $sp0748b4; $sp5588d7->discount_type = $spf50c9c; if ($sp2d4d5b === \App\Coupon::TYPE_REPEAT) { $sp5588d7->count_all = (int) $sp375069->post('count_all', 1); if ($sp5588d7->count_all < 1 || $sp5588d7->count_all > 10000000) { return Response::fail('可用次数不能超过10000000'); } } if ($sp92c143) { $sp5588d7->expire_at = $sp92c143; } $sp5588d7->saveOrFail(); } else { $sp5e7d21 = explode('
', $sp696fca); for ($sp9d4bce = 0; $sp9d4bce < count($sp5e7d21); $sp9d4bce++) { $sp5dfe59 = str_replace('', '', trim($sp5e7d21[$sp9d4bce])); $sp5588d7 = new \App\Coupon(); $sp5588d7->coupon = $sp5dfe59; $sp5588d7->category_id = $spca39ca; $sp5588d7->product_id = $sp138ddb; $sp5588d7->status = $spfc8a83; $sp5588d7->type = $sp2d4d5b; $sp5588d7->discount_val = $sp0748b4; $sp5588d7->discount_type = $spf50c9c; $sp5e7d21[$sp9d4bce] = $sp5588d7; } \App\Product::find($sp138ddb)->coupons()->saveMany($sp5e7d21); } return Response::success(); } function enable(Request $sp375069) { $this->validate($sp375069, array('ids' => 'required|string', 'enabled' => 'required|integer|between:0,1')); $sp630e91 = $sp375069->post('ids'); $spedd1a7 = (int) $sp375069->post('enabled'); $this->authQuery($sp375069, \App\Coupon::class)->whereIn('id', explode(',', $sp630e91))->update(array('enabled' => $spedd1a7)); return Response::success(); } function delete(Request $sp375069) { $this->validate($sp375069, array('ids' => 'required|string')); $sp630e91 = $sp375069->post('ids'); $this->authQuery($sp375069, \App\Coupon::class)->whereIn('id', explode(',', $sp630e91))->delete(); return Response::success(); } }