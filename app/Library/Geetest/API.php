<?php
namespace App\Library\Geetest; use App\Library\Helper; use Hashids\Hashids; use Illuminate\Support\Facades\Session; class API { private $geetest_conf = null; public function __construct($spa67d5b) { $this->geetest_conf = $spa67d5b; } public static function get() { $sp39113c = config('services.geetest.id'); $spbccf24 = config('services.geetest.key'); if (!strlen($sp39113c) || !strlen($spbccf24)) { return array('message' => 'geetest error: no config'); } $sp055fa0 = new Lib($sp39113c, $spbccf24); $spc2f05b = time() . rand(1, 10000); $speb8ab3 = $sp055fa0->pre_process($spc2f05b); $sp6706d8 = json_decode($sp055fa0->get_response_str(), true); $sp6706d8['key'] = Helper::id_encode($spc2f05b, 3566, $speb8ab3); return $sp6706d8; } public static function verify($sp698e3b, $sp588707, $sp756e18, $sp1eab65) { $sp055fa0 = new Lib(config('services.geetest.id'), config('services.geetest.key')); Helper::id_decode($sp698e3b, 3566, $sp2eb9d6); $spc2f05b = $sp2eb9d6[1]; $speb8ab3 = $sp2eb9d6[4]; if ($speb8ab3 === 1) { $spc4e359 = $sp055fa0->success_validate($sp588707, $sp756e18, $sp1eab65, $spc2f05b); if ($spc4e359) { return true; } else { return false; } } else { if ($sp055fa0->fail_validate($sp588707, $sp756e18, $sp1eab65)) { return true; } else { return false; } } } }