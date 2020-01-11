<?php
namespace App\Http\Controllers\Admin; use App\Library\FundHelper; use App\Library\Helper; use Carbon\Carbon; use Illuminate\Database\Eloquent\Relations\Relation; use Illuminate\Http\Request; use App\Http\Controllers\Controller; use App\Library\Response; use Illuminate\Support\Facades\DB; use Illuminate\Support\Facades\Log; class Order extends Controller { public function delete(Request $sp375069) { $this->validate($sp375069, array('ids' => 'required|string', 'income' => 'required|integer', 'balance' => 'required|integer')); $sp630e91 = $sp375069->post('ids'); $sp2dc0c5 = (int) $sp375069->post('income'); $spe35485 = (int) $sp375069->post('balance'); \App\Order::whereIn('id', explode(',', $sp630e91))->chunk(100, function ($sp46f34e) use($sp2dc0c5, $spe35485) { foreach ($sp46f34e as $spf6b161) { $spf6b161->cards()->detach(); try { if ($sp2dc0c5) { $spf6b161->fundRecord()->delete(); } if ($spe35485) { $sp264a55 = \App\User::lockForUpdate()->firstOrFail(); $sp264a55->m_all -= $spf6b161->income; $sp264a55->saveOrFail(); } $spf6b161->delete(); } catch (\Exception $spf95c2c) { } } }); return Response::success(); } function freeze(Request $sp375069) { $this->validate($sp375069, array('ids' => 'required|string')); $sp630e91 = explode(',', $sp375069->post('ids')); $spa91cd9 = $sp375069->post('reason'); $spbefb16 = 0; $sp2b0456 = 0; foreach ($sp630e91 as $sp4f5d8e) { $spbefb16++; if (FundHelper::orderFreeze($sp4f5d8e, $spa91cd9)) { $sp2b0456++; } } return Response::success(array($spbefb16, $sp2b0456)); } function unfreeze(Request $sp375069) { $this->validate($sp375069, array('ids' => 'required|string')); $sp630e91 = explode(',', $sp375069->post('ids')); $spbefb16 = 0; $sp2b0456 = 0; $spcd458b = \App\Order::STATUS_FROZEN; foreach ($sp630e91 as $sp4f5d8e) { $spbefb16++; if (FundHelper::orderUnfreeze($sp4f5d8e, '后台操作', null, $spcd458b)) { $sp2b0456++; } } return Response::success(array($spbefb16, $sp2b0456, $spcd458b)); } function set_paid(Request $sp375069) { $this->validate($sp375069, array('id' => 'required|integer')); $sp39113c = $sp375069->post('id', ''); $spbd251c = $sp375069->post('trade_no', ''); if (strlen($spbd251c) < 1) { return Response::forbidden('请输入支付系统内单号'); } $spf6b161 = \App\Order::findOrFail($sp39113c); if ($spf6b161->status !== \App\Order::STATUS_UNPAY) { return Response::forbidden('只能操作未支付订单'); } $spe32066 = 'Admin.SetPaid'; $sp4b6ac9 = $spf6b161->order_no; $sp46b577 = $spf6b161->paid; try { Log::debug($spe32066 . " shipOrder start, order_no: {$sp4b6ac9}, amount: {$sp46b577}, trade_no: {$spbd251c}"); (new \App\Http\Controllers\Shop\Pay())->shipOrder($sp375069, $sp4b6ac9, $sp46b577, $spbd251c); Log::debug($spe32066 . ' shipOrder end, order_no: ' . $sp4b6ac9); $sp2b0456 = true; $spa01063 = '发货成功'; } catch (\Exception $spf95c2c) { $sp2b0456 = false; $spa01063 = $spf95c2c->getMessage(); Log::error($spe32066 . ' shipOrder Exception: ' . $spf95c2c->getMessage()); } $spf6b161 = \App\Order::with(array('pay' => function (Relation $sped9569) { $sped9569->select(array('id', 'name')); }, 'card_orders.card' => function (Relation $sped9569) { $sped9569->select(array('id', 'card')); }))->findOrFail($sp39113c); if ($spf6b161->status === \App\Order::STATUS_PAID) { if ($spf6b161->product->delivery === \App\Product::DELIVERY_MANUAL) { $sp2b0456 = true; $spa01063 = '已标记为付款成功<br>当前商品为手动发货商品, 请手动进行发货。'; } else { $sp2b0456 = false; $spa01063 = '已标记为付款成功, <br>但是买家库存不足, 发货失败, 请稍后尝试手动发货。'; } } return Response::success(array('code' => $sp2b0456 ? 0 : -1, 'msg' => $spa01063, 'order' => $spf6b161)); } }