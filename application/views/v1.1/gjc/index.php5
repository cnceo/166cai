<!doctype html> 
<html> 
<head> 
<meta charset="utf-8"> 
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui"/> 
<meta>
<title>欧洲杯-166彩票网</title>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/global.min.css');?>">
<style>
	body {
		background: #131a35;
		color: #fff;
	}
	
	.euro-wrap {
		background: url(../../caipiaoimg/v1.1/img/active/euro/euro-bg.jpg) 50% 0 no-repeat;
		font-size: 14px;
	}
	.euro-wrap em {
		color: #ff8e33;
	}

	.euro-wrap .multi-modifier a {
		width: 38px;
		height: 38px;
		background: #0c143b;
		border: 3px solid #233061;
		line-height: 38px;
		font-size: 28px;
		color: #4d5aa0;
	}
	.euro-wrap .multi-modifier label {
		width: 62px;
		height: 38px;
		background: #233061;
		border: 3px solid #233061;
	}
	.euro-wrap .multi-modifier label input {
		width: 100%;
		height: 38px;
		padding: 0;
		background: #233061;
		text-align: center;
		line-height: 38px;
		font-size: 16px;
		color: #ccd4ff;
	}

	.euro-wrap hr {
		height: 0;
		margin: 10px auto 20px;
		background: #0a0f23;
		border: none;
		border-bottom: 1px dashed #1c2545;
	}
	.euro-hd {
		height: 480px;
		background: url(../../caipiaoimg/v1.1/img/active/euro/euro-banner.jpg) 50% 0 no-repeat;
		text-indent: -150%;
		overflow: hidden;
		font-size: 0;
	}
	.euro-tab {
		margin-bottom: 40px;
	}
	.euro-tab li {
		float: left;
		width: 50%;
		height: 60px;
		background: #3c509a;
		text-align: center;
		line-height: 60px;
		font-weight: bold;
		font-size: 18px;
		color: #d9e1ff;
		cursor: pointer;
	}
	.euro-tab li.current {
		position: relative;
		top: -4px;
		height: 68px;
		background: #ffbd59;
		line-height: 68px;
		color: #7d1944;
		*zoom: 1;
	}
	.euro-tab li.current:after {
		content: '';
		position: absolute;
		left: 50%;
		bottom: -7px;
		border-width: 7px 8px 0;
		border-style: solid;
		border-color: #ffbd59 transparent transparent;
	}
	.euro-tab li i {
		display: inline-block;
		*display: inline;
		*zoom: 1;
		width: 30px;
		height: 30px;
		margin-right: 10px;
		background: url(../../caipiaoimg/v1.1/img/active/euro/sprite-icon.png) -40px -50px;
		text-align: center;
		line-height: 30px;
		color: #5165ad;
	}
	.euro-tab li.current i {
		background-position: 0 -50px;
		color: #ffd596;
	}
	.euro-tab li b {
		font-weight: bold;
		font-size: 24px;
	}
	.euro-con h2 {
		height: 72px;
		line-height: 72px;
		font-size: 32px;
		color: #fff;
	}

	.euro-item {
		display: none;
	}

	.euro-group {
		overflow: hidden;
		*zoom: 1;
	}
	.euro-group-list {
		*float: left;
		margin-right: -40px;
	}
	.euro-group-item {
		float: left;
		width: 480px;
		margin: 0 40px 40px 0;
		background: #141c3d;
		border-top: 2px solid #283771;
	}

	.euro-group-caption {
		height: 40px;
		text-align: center;
		line-height: 40px;
		font-weight: bold;
		font-size: 18px;
	}
	.euro-group-thead {
		height: 30px;
		background: #0d1431;
		text-align: center;
		font-weight: bold;
		line-height: 30px;
	}
	.euro-group-tr {
		overflow: hidden;
		*zoom: 1;
		border-top: 1px solid #2c3761;
		border-bottom: 1px solid #0f1a43;
	}
	.special-team .col1, .special-team .col2 {
		color: #ff8e33;
	}
	.euro-group-item .col1, .euro-group-item .col2, .euro-group-item .col3 {
		float: left;
	}
	.euro-group-item .col1 {
		width: 200px;
	}
	.euro-group-item .col2, .euro-group-item .col3 {
		width: 120px;
		text-align: center;
	}
	.euro-group-tbody {
		overflow: hidden;
		padding: 0 20px;
		background: #1e284e;
	}
	.euro-group-tbody ul {
		margin-top: -1px;
	}
	.euro-group-tbody .euro-group-tr {
		height: 65px;
		line-height: 65px;
	}
	.euro-group-tbody .euro-group-tr i {
		margin-right: 8px;
		vertical-align: -16px;
	}
	.euro-group-tbody .col1 {
		width: 180px;
		font-size: 18px;
	}
	.euro-group-tbody .col2 {
		font-weight: bold;
		font-size: 18px;
	}
	.euro-group-tbody .btn-ss {
		height: 30px;
		padding: 0 18px;
		background: transparent;
		border: 1px solid #8690b3;
		line-height: 30px;
		color: #fff;
	}
	.euro-group-tbody li.selected, .euro-group-tbody li.king {
		position: relative;
	}
	.euro-group-tbody .btn-ss:hover {
		background: #ffb340;
		border-color: #ffb340;
	}
	.euro-group-tbody .btn-disabled {
		background: #666 !important;
		border-color: #666 !important;
	}
	.euro-group-tbody li.selected .btn-ss {
		background: #e56600;
		border-color: #e56600;
	}
	.euro-group-tbody li.selected:after, .euro-group-tbody li.king:after {
		content: '';
		position: absolute;
		left: 2px;
		top: 2px;
		width: 60px;
		height: 56px;
		background: url(../../caipiaoimg/v1.1/img/active/euro/sprite-icon.png) -60px -80px no-repeat;
	}
	.euro-group-tbody li.king .col3 span {
		display: block;
		width: 58px;
		height: 44px;
		margin: 11px auto 0;
		background: url(../../caipiaoimg/v1.1/img/active/euro/sprite-icon.png) -20px -137px no-repeat;
		text-indent: -150%;
		overflow: hidden;
		font-size: 0;
	}
	.euro-group-tbody .match-pass, .euro-group-tbody .match-suspend .col1, .euro-group-tbody .match-suspend .col2 {
		opacity: .2;
		filter: alpha(opacity=20);
	}

	.euro-bet-bar-placeholder {
		height: 120px;
	}

	.euro-item2 .euro-bet-bar-placeholder {
		margin: 10px 0;
	}

	.euro-bet-bar {
		width: 100%;
		height: 100px;
		padding: 10px 0;
		background: #131a35;
		line-height: 44px;
		font-size: 18px;
	}
	.euro-bet-bar .wrap {
		position: relative;
	}

	.btn-bet-now {
		position: absolute;
		top: 50%;
		right: 0;
		height: 52px;
		margin-top: -26px;
		padding: 0 48px;
		background: #ffba55;
		border-bottom: 2px solid #b65611;
		border-radius: 100px;
		line-height: 48px;
		font-weight: bold;
		font-size: 18px;
		color: #7d1944;
	}
	.btn-bet-now:hover {
		background: #ffae38;
		text-decoration: none;
		color: #7d1944;
	}


	.euro-forecast {
		padding: 50px 0 10px;
		background: #0a0f23;
		line-height: 2;
		color: #d9e1ff;
	}

	.euro-forecast h3 {
		margin: 20px 0 8px;
		font-weight: bold;
		font-size: 18px;
		color: #bfcdff;
	}

	.fzqk li {
		float: left;
		width: 320px;
	}

	.ttssz {
		position: relative;
		width: 1000px;
		height: 500px;
		background: url(../../caipiaoimg/v1.1/img/active/euro/against-plan-bg.png) 50% 0 no-repeat;
	}

	.ttssz li {
		position: absolute;
		width: 110px;
		height: 40px;
		background: #111a3e;
		text-align: center;
		line-height: 40px;
		color: #d9e1ff;
	}
	.ttssz li i {
		float: left;
		width: 20px;
		height: 20px;
		margin: 10px 0 0 10px;
		_display: inline;
	}
	.ttsqd-l1 {
		left: 0;
		top: 9px;
	}
	.ttsqd-l2 {
		left: 0;
		top: 59px;
	}
	.ttsqd-l3 {
		left: 0;
		top: 139px;
	}
	.ttsqd-l4 {
		left: 0;
		top: 189px;
	}
	.ttsqd-l5 {
		left: 0;
		top: 269px;
	}
	.ttsqd-l6 {
		left: 0;
		top: 319px;
	}
	.ttsqd-l7 {
		left: 0;
		top: 399px;
	}
	.ttsqd-l8 {
		left: 0;
		top: 449px;
	}
	.ttsqd-l21 {
		left: 125px;
		top: 34px;
	}
	.ttsqd-l22 {
		left: 125px;
		top: 164px;
	}
	.ttsqd-l23 {
		left: 125px;
		top: 294px;
	}
	.ttsqd-l24 {
		left: 125px;
		top: 424px;
	}
	.ttsqd-r21 {
		right: 125px;
		top: 34px;
	}
	.ttsqd-r22 {
		right: 125px;
		top: 164px;
	}
	.ttsqd-r23 {
		right: 125px;
		top: 294px;
	}
	.ttsqd-r24 {
		right: 125px;
		top: 424px;
	}
	.ttsqd-r31 {
		left: 250px;
		top: 100px;
	}
	.ttsqd-r32 {
		left: 250px;
		top: 360px;
	}
	.ttsqd-l41 {
		left: 375px;
		top: 229px;
	}
	.ttsqd-c {
		left: 50%;
		top: 94px;
		margin-left: -55px;
	}
	.ttsqd-r41 {
		right: 375px;
		top: 229px;
	}
	.ttsqd-l31 {
		right: 250px;
		top: 100px;
	}
	.ttsqd-l32 {
		right: 250px;
		top: 360px;
	}
	.ttsqd-r1 {
		right: 0;
		top: 9px;
	}
	.ttsqd-r2 {
		right: 0;
		top: 59px;
	}
	.ttsqd-r3 {
		right: 0;
		top: 139px;
	}
	.ttsqd-r4 {
		right: 0;
		top: 189px;
	}
	.ttsqd-r5 {
		right: 0;
		top: 269px;
	}
	.ttsqd-r6 {
		right: 0;
		top: 319px;
	}
	.ttsqd-r7 {
		right: 0;
		top: 399px;
	}
	.ttsqd-r8 {
		right: 0;
		top: 449px;
	}

	.euro-finals-forecast h3 {
		height: 53px;
		line-height: 53px;
		font-weight: bold;
		font-size: 18px;
		color: #bfcdff;
	}

	.euro-finals-forecast hr {
		margin: 0;
	}
	
	.euro-finals-list {
		margin-bottom: 30px;
		overflow: hidden;
		*zoom: 1;
	}
	.euro-finals-list ul {
		*float: left;
		margin-right: -20px;
	}
	.euro-finals-list li {
		float: left;
	}
	.euro-finals-table {
		width: 235px;
		margin-right: 20px;
		text-align: center;
	}
	.euro-finals-head {
		height: 36px;
		padding-left: 35px;
		border-top: 1px solid #212e5f;
		line-height: 36px;
		color: #596fc4;
	}
	.euro-finals-head span, .euro-finals-body a span {
		float: left;
		width: 150px;
	}
	.euro-finals-head s, .euro-finals-body a s {
		float: left;
		width: 48px;
	}
	.euro-finals-body a {
		position: relative;
		display: block;
		height: 35px;
		margin-bottom: 10px;
		padding-left: 35px;
		border: 1px dashed #212e5f;
		line-height: 35px;
		font-size: 14px;
		color: #d9e1ff;
	}
	.euro-finals-body a i {
		position: absolute;
		left: 10px;
		top: 50%;
		width: 20px;
		height: 20px;
		margin-top: -10px;
		background: url(../../caipiaoimg/v1.1/img/active/euro/sprite-icon.png) 0 -90px no-repeat;
	}
	.euro-finals-body a:hover {
		background: #233061;
		border: 1px solid #233061;
		text-decoration: none;
	}
	.euro-finals-body a.selected, .euro-finals-body a.eure-finals {
		background: #ffba55;
		border: 1px solid #ffba55;
		color: #7d1944;
	}
	.euro-finals-body a.selected i {
		background-position: -40px -90px;
	}
	.euro-finals-body a.eure-finals i {
		left: 3px;
		top: -1px;
		margin-top: 0;
		width: 34px;
		height: 31px;
		background-position: 0 -180px;
	}
	.euro-finals-body .euro-eliminate {
		opacity: .4;
		filter: alpha(opacity=40);
	}
	.euro-finals-body .euro-eliminate:hover {
		border-style: dashed;
		background: #131a35;
	}

	.euro-bet-area {
		position: relative;
		height: 112px;
		margin: 0 auto 20px;
		padding-top: 300px;
		background: #141f49 url(../../caipiaoimg/v1.1/img/active/euro/euro-bet-area-bg.png) 50% 80px no-repeat;
		text-align: center;
	}
	.euro-bet-area a {
		position: absolute;
		top: 60px;
		width: 180px;
		height: 200px;
		background: #233061;
		font-size: 18px;
		color: #bfcdff;
	}
	.euro-bet-img {
		position: relative;
		width: 108px;
		height: 108px;
		margin: 22px auto;
	}
	.euro-bet-img i {
		display: none;
		position: absolute;
		left: 0;
		top: 0;
		width: 100%;
		height: 108px;
		background: url(../../caipiaoimg/v1.1/img/active/euro/euro-bet-img-mask.png) 50% 50% no-repeat;
		text-align: center;
		line-height: 108px;
		font-size: 14px;
		color: #fff;
	}
	.euro-bet-area a:hover {
		background: #2e3d79;
		text-decoration: none;
	}
	.euro-bet-area a:hover .euro-bet-img i {
		display: block;
	}
	.euro-bet-area a b {
		display: block;
		margin-top: 36px;
		line-height: 1;
		font-size: 80px;
		color: #89a2ff;
	}
	.euro-bet-note em {
		display: block;
		line-height: 2;
	}

	.euro-bet-note b {
		font-size: 18px;
	}

	.euro-bet-l {
		left: 50%;
		margin-left: -300px;
	}
	.euro-bet-r {
		right: 50%;
		margin-right: -300px;
	}
	.euro-pop-mask {
		position: fixed;
		left: 0;
		top: 0;
		z-index: 989;
		width: 100%;
		height: 100%;
		background: #000;
		opacity: .5;
		filter: alpha(opacity=50);
	}
	.euro-pop {
		position: fixed;
		left: 50%;
		top: 50%;
		z-index: 990;
		width: 870px;
		height: 550px;
		margin: -275px 0 0 -435px;
		;
		background: #304594;
	}
	.euro-pop-hd {
		position: relative;
	}
	.euro-pop-bd {
		padding: 20px 30px
	}
	.euro-pop-close {
		position: absolute;
		right: -50px;
		top: -4px;
		width: 40px;
		height: 40px;
		background: url(../../caipiaoimg/v1.1/img/active/euro/sprite-icon.png) 0 0 no-repeat;
		text-indent: -150%;
		overflow: hidden;
		font-size: 0;
	}
	.euro-pop-ft {
		text-align: center;
	}
	.euro-pop-team {
		overflow: hidden;
		*zoom: 1;
	}
	.euro-pop-team ul {
		*float: left;
		margin-right: -20px;
	}
	.euro-pop-team li {
		float: left;
		width: 390px;
		margin: 0 20px 20px 0;
	}
	.euro-pop-title {
		height: 32px;
		line-height: 32px;
		text-align: center;
		font-size: 14px;
		color: #596fc4;
	}
	.euro-pop-item {
		float: left;
		margin-right: -10px;
	}
	.euro-pop-item a {
		float: left;
		width: 72px;
		height: 82px;
		margin-right: 10px;
		padding: 8px;
		border: 1px dashed #4073c8;
		text-align: center;
		color: #fff;
	}
	.euro-pop-item a.euro-pop-eliminate, .euro-pop-item a.euro-pop-suspend {
		position: relative;
		color: #98a2ca;
		cursor: default;
	}
	.euro-pop-item a.euro-pop-eliminate:hover, .euro-pop-item a.euro-pop-suspend:hover {
		background: #304594;
	}
	.item-logo a.euro-pop-eliminate i, .item-logo a.euro-pop-suspend i {
		opacity: .4;
		filter: alpha(opacity=40);
	}
	.euro-pop-item a.euro-pop-eliminate b {
		position: absolute;
		right: 0;
		bottom: 0;
		z-index: 10;
		width: 58px;
		height: 44px;
		background: url(../../caipiaoimg/v1.1/img/active/euro/sprite-icon.png) -60px -176px no-repeat
	}
	.euro-pop-item a.selected{
		position: relative;
		background: #213376;
	}
	.euro-pop-item a.selected:after {
		content: '';
		position: absolute;
		right: 0;
		top: 0;
		width: 16px;
		height: 16px;
		background: url(../../caipiaoimg/v1.1/img/active/euro/sprite-icon.png) 0 -120px no-repeat
	}
	.euro-pop-item a:hover {
		background-color: #213376;
		text-decoration: none;
	}
	.item-logo i {
		display: inline-block;
		*display: inline;
		*zoom: 1;
		width: 48px;
		height: 48px;
		background: url(../../caipiaoimg/v1.1/img/active/euro/sprite-country.png) no-repeat;
	}
	.item-logo .faguo i {
		background-position: 0 0;
	}
	.item-logo .luomaniya i {
		background-position: -50px 0;
	}
	.item-logo .aerbaniya i {
		background-position: -100px 0;
	}
	.item-logo .ruishi i {
		background-position: -150px 0;
	}
	.item-logo .yinggelan i {
		background-position: 0 -50px;
	}
	.item-logo .eluosi i {
		background-position: -50px -50px;
	}
	.item-logo .weiershi i {
		background-position: -100px -50px;
	}
	.item-logo .siluofake i {
		background-position: -150px -50px;
	}
	.item-logo .deguo i {
		background-position: 0 -100px;
	}
	.item-logo .wukelan i {
		background-position: -50px -100px;
	}
	.item-logo .bolan i {
		background-position: -100px -100px;
	}
	.item-logo .beiaierlan i {
		background-position: -150px -100px;
	}
	.item-logo .xibanya i {
		background-position: 0 -150px;
	}
	.item-logo .jieke i {
		background-position: -50px -150px;
	}
	.item-logo .tuerqi i {
		background-position: -100px -150px;
	}
	.item-logo .keluodiya i {
		background-position: -150px -150px;
	}
	.item-logo .bilishi i {
		background-position: 0 -200px;
	}
	.item-logo .yidali i {
		background-position: -50px -200px;
	}
	.item-logo .aierlan i {
		background-position: -100px -200px;
	}
	.item-logo .ruidian i {
	 	background-position: -150px -200px;
	}
	.item-logo .putaoya i {
		background-position: 0 -250px;
	}
	.item-logo .bingdao i {
		background-position: -50px -250px;
	}
	.item-logo .aodili i {
		background-position: -100px -250px;
	}
	.item-logo .xiongyali i {
		background-position: -150px -250px;
	}

	.item-logo-s i {
		display: inline-block;
		*display: inline;
		*zoom: 1;
		width: 20px;
		height: 20px;
		background: url(../../caipiaoimg/v1.1/img/active/euro/sprite-country-s.png) 100px 0 no-repeat;
	}
	.item-logo-s .faguo i {
		background-position: 0 0;
	}
	.item-logo-s .luomaniya i {
		background-position: -30px 0;
	}
	.item-logo-s .aerbaniya i {
		background-position: -60px 0;
	}
	.item-logo-s .ruishi i {
		background-position: -90px 0;
	}
	.item-logo-s .yinggelan i {
		background-position: 0 -30px;
	}
	.item-logo-s .eluosi i {
		background-position: -30px -30px;
	}
	.item-logo-s .weiershi i {
		background-position: -60px -30px;
	}
	.item-logo-s .siluofake i {
		background-position: -90px -30px;
	}
	.item-logo-s .deguo i {
		background-position: 0 -60px;
	}
	.item-logo-s .wukelan i {
		background-position: -30px -60px;
	}
	.item-logo-s .bolan i {
		background-position: -60px -60px;
	}
	.item-logo-s .beiaierlan i {
		background-position: -90px -60px;
	}
	.item-logo-s .xibanya i {
		background-position: 0 -90px;
	}
	.item-logo-s .jieke i {
		background-position: -30px -90px;
	}
	.item-logo-s .tuerqi i {
		background-position: -60px -90px;
	}
	.item-logo-s .keluodiya i {
		background-position: -90px -90px;
	}
	.item-logo-s .bilishi i {
		background-position: 0 -120px;
	}
	.item-logo-s .yidali i {
		background-position: -30px -120px;
	}
	.item-logo-s .aierlan i {
		background-position: -60px -120px;
	}
	.item-logo-s .ruidian i {
		background-position: -90px -120px;
	}
	.item-logo-s .putaoya i {
		background-position: 0 -150px;
	}
	.item-logo-s .bingdao i {
		background-position: -30px -150px;
	}
	.item-logo-s .aodili i {
		background-position: -60px -150px;
	}
	.item-logo-s .xiongyali i {
		background-position: -90px -150px;
	}
	.euro-pop-item a i {
		display: block;
		margin: 0 auto 8px;
		
	}
	.euro-pop-btn {
		display: inline-block;
		*display: inline;
		*zoom: 1;
		width: 120px;
		height: 34px;
		background: #ffba55;
		border-radius: 50px;
		text-align: center;
		line-height: 34px;
		font-size: 16px;
		color: #7d1944;
	}
	.euro-pop-btn:hover {
		background: #ffae38;
		text-decoration: none;
		color: #7d1944;
	}

	.euro-item1-icom, .euro-item2-icom {
		display: inline-block;
		*display: inline;
		*zoom: 1;
		vertical-align: -6px;
		*vertical-align: 3px;
		width: 32px;
		height: 32px;
		margin-right: 8px;
		background: url(../../caipiaoimg/v1.1/img/active/euro/sprite-icon.png) no-repeat;
	}
	.euro-item1-icom {
		background-position: -80px 0;
	}
	.euro-item2-icom {
		background-position: -40px 0;
	}
	.euro-item2 .euro-item-hd h2, .euro-item2-sub {
		display: inline-block;
		*display: inline;
		*zoom: 1;
	}
	.euro-item2-sub {
		position: relative;
		vertical-align: 8px;
		height: 23px;
		line-height: 23px;
		margin-left: 8px;
		padding: 0 10px;
		background: #ca610d;
		border-radius: 2px;
	}
	.euro-item2-sub  i {
		position: absolute;
		left: -3px;
		top: 4px;
		width: 0;
		height: 0;
		border-width: 3px 3px 3px;
		border-style: solid;
		border-color: transparent transparent #ca610d;
		_border-style: dahsed dahsed solid;
	}
	.fixed {
		_position: static;
		top: auto;
		bottom: 0;
		z-index: 99;
	}
	.euro-item2 .fixed {
		top: 0;
		bottom: auto;
	}
	.pop-body {
		color:#666;
	}
</style>
<?php 
$staClass = array(
	'1' => 'match-pass',
	'2' => 'king',
	'3' => 'match-suspend'
);
$ystaClass = array(
	'1' => 'euro-eliminate',
	'2' => 'eure-finals',
	'3' => 'euro-eliminate'
);
?>
<script type="text/javascript">
var baseUrl = '<?php echo $this->config->item('base_url'); ?>';
</script>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js');?>" type="text/javascript"></script>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/base.min.js'); ?>" type="text/javascript" ></script>
</head>
<body>
	<?php if (empty($this->uid)): ?>
	    <div class="top_bar">
	    	<?php $this->load->view('v1.1/elements/common/header_topbar_notlogin'); ?>
	    </div>
	<?php else: ?>
	    <div class="top_bar">
	        <?php $this->load->view('v1.1/elements/common/header_topbar'); ?>
	    </div>
	<?php endif; ?>
	</div>
	<div class="euro-wrap">
		<div class="euro-hd">
			<div class="wrap">
				<h1>2016年欧洲杯</h1>
				<p>比赛时间6月11日-7月11日</p>
			</div>		
		</div>
		<div class="euro-bd">
			<div class="wrap euro-tab">
				<ul class="clearfix">
					<li class="current"><i>1</i><b>冠军彩</b>玩法投注</li>
					<li><i>2</i><b>冠亚军彩</b>玩法投注</li>
				</ul>
			</div>
			<div class="euro-con">
				<div class="euro-item" style="display: block;">
					<div class="wrap">
						<h2><i class="euro-item1-icom"></i>冠军彩玩法投注</h2>
						<div class="euro-group item-logo">
							<ul class="euro-group-list">
							<?php foreach ($gjList as $group => $gjs){?>
								<li class="euro-group-item">
									<div class="euro-group-caption"><?php echo $group?>组</div>
									<div class="euro-group-table">
										<div class="euro-group-thead">
											<div class="euro-group-tr">
												<span class="col1">球队</span>
												<span class="col2">赔率</span>
												<span class="col3">投注</span>
											</div>
										</div>
										<div class="euro-group-tbody">
											<ul>
											<?php foreach ($gjs as $mid => $gj) {?> 
												<li class="euro-group-tr <?php echo $gj['logo']?> <?php echo in_array($gj['status'], array(1, 2, 3)) ?$staClass[$gj['status']] : ''?> <?php if (in_array($gj['logo'], array('faguo', 'yinggelan', 'deguo', 'bilishi', 'xibanya', 'putaoya', 'keluodiya', 'yidali'))) {echo 'special-team';}?>" 
												data-name="<?php echo $gj['name']?>" data-odd='<?php echo str_replace('.', '', $gj['odds'])?>' data-index="<?php echo $mid?>">
													<div class="col1"><i></i><?php echo $gj['name']?>(<?php echo $gj['rank']?>)</div>
													<span class="col2"><?php echo $gj['odds']?></span>
													<div class="col3">
													<?php if ($gj['status'] == 1) {?>
														已被淘汰
													<?php }elseif ($gj['status'] == 2) {?>
														<span>冠军</span>
													<?php }elseif ($gj['status'] == 3) {?>
														<a class="btn-ss btn-disabled">停售</a>
													<?php }else {?>
														<a href="javascript:;" class="btn-ss">夺冠</a>
													<?php }?>
													</div>
												</li>
											<?php }?> 
											</ul>
										</div>
									</div>
								</li>			
							<?php } ?>
							</ul>
						</div>
					</div>
					<div class="euro-bet-bar-placeholder">
						<div class="euro-bet-bar">
							<div class="wrap">
								<p class="gj-txt">您还尚未选择球队</p>
								<div class="gj-modifier">
									投注倍数
									<div class="multi-modifier">
			                            <a href="javascript:;" class="minus">-</a>
			                            <label><input class="multi number" type="text" value="1"></label>
			                            <a href="javascript:;" class="plus" data-max="100000">+</a>
			                        </div>
									投注金额<em>0</em>元，预测奖金<em>0</em>元
								</div>
								<?php if($lotteryConfig[GJ]['status']) {?>
									<a href="javascript:;" class="btn-bet-now submit <?php echo $showBind ? ' not-bind': '';?>" data-type="gj">立即预约</a>
								<?php }else {?>
									<a href="javascript:;" class="btn-bet-now btn-disabled <?php echo $showBind ? ' not-bind': '';?>" data-type="gj">暂停预约</a>
								<?php }?>
							</div>
						</div>
					</div>
					<div class="euro-forecast">
						<div class="wrap">
							<h2><i></i>欧洲杯预测前瞻</h2>
							<p>想要完成对欧洲杯冠军队伍以及入围决赛队伍的有效预测，首先得对分组情况及淘汰赛规则做好功课。</p>
							<hr>
							<h3>分组情况：</h3>
							<ul class="fzqk clearfix">
								<li>A组：法国、罗马尼亚、阿尔巴尼亚、瑞士</li>
								<li>B组：英格兰、俄罗斯、威尔士、斯洛伐克</li>
								<li>C组：德国、乌克兰、波兰、北爱尔兰</li>
								<li>D组：西班牙、捷克、土耳其、克罗地亚</li>
								<li>E组：比利时、意大利、爱尔兰、瑞典</li>
								<li>F组：葡萄牙、冰岛、奥地利、匈牙利 </li>
							</ul>
							<h3>分组前瞻：</h3>
							<p>东道主法国的运气极佳，他们先迎来了第四档中最弱的阿尔巴尼亚。葡萄牙的运气也是很好，他们和冰岛、奥地利、匈牙利同组。最郁闷的当属比利时和西班牙，比利时不但抽到了第二档中最强的意大利，普遍认为第三档最强的瑞典和第四档最强的爱尔兰也都进入其所在的E组，看起来倒像是特地要检验一下目前国际排名世界第一的比利时的真正成色,这一小组也被许多人认为是死亡之组。西班牙则与捷克、土耳其和克罗地亚同组，也算是一个“准死亡之组”</p>
							<hr>
							<h3>淘汰赛赛制：</h3>
							<div class="ttssz">
								<ul class="item-logo-s">
									<li class="ttsqd-l1"><?php echo empty($ttRes[37]['hid']) ? 'A2' : $ttRes[37]['home']?></li>
									<li class="ttsqd-l2"><?php echo empty($ttRes[37]['hid']) ? 'C2' : $ttRes[37]['away']?></li>
									<li class="ttsqd-l3"><?php echo empty($ttRes[39]['hid']) ? 'D1' : $ttRes[39]['home']?></li>
									<li class="ttsqd-l4"><?php echo empty($ttRes[39]['hid']) ? 'B3/E3/F3' : $ttRes[39]['away']?></li>
									<li class="ttsqd-l5"><?php echo empty($ttRes[38]['hid']) ? 'B1' : $ttRes[38]['home']?></li>
									<li class="ttsqd-l6"><?php echo empty($ttRes[38]['hid']) ? 'A3/C3/D3' : $ttRes[38]['away']?></li>
									<li class="ttsqd-l7"><?php echo empty($ttRes[42]['hid']) ? 'F1' : $ttRes[42]['home']?></li>
									<li class="ttsqd-l8"><?php echo empty($ttRes[42]['hid']) ? 'E2' : $ttRes[42]['away']?></li>
									<li class="ttsqd-l21"><?php echo empty($ttRes[45]['hid']) ? '1/8决赛' : $ttRes[45]['home']?></li>
									<li class="ttsqd-l22"><?php echo empty($ttRes[45]['hid']) ? '1/8决赛' : $ttRes[45]['away']?></li>
									<li class="ttsqd-l23"><?php echo empty($ttRes[46]['hid']) ? '1/8决赛' : $ttRes[46]['home']?></li>
									<li class="ttsqd-l24"><?php echo empty($ttRes[46]['hid']) ? '1/8决赛' : $ttRes[46]['away']?></li>
									<li class="ttsqd-l31"><?php echo empty($ttRes[50]['hid']) ? '1/4决赛' : $ttRes[50]['home']?></li>
									<li class="ttsqd-l32"><?php echo empty($ttRes[50]['hid']) ? '1/4决赛' : $ttRes[50]['away']?></li>
									<li class="ttsqd-l41"><?php echo empty($ttRes[51]['hid']) ? '半决赛' : $ttRes[51]['home']?></li>
									<li class="ttsqd-c">决赛</li>
									<li class="ttsqd-r41"><?php echo empty($ttRes[51]['hid']) ? '半决赛' : $ttRes[51]['away']?></li>
									<li class="ttsqd-r32"><?php echo empty($ttRes[49]['hid']) ? '1/4决赛' : $ttRes[49]['away']?></li>
									<li class="ttsqd-r31"><?php echo empty($ttRes[49]['hid']) ? '1/4决赛' : $ttRes[49]['home']?></li>
									<li class="ttsqd-r21"><?php echo empty($ttRes[47]['hid']) ? '1/8决赛' : $ttRes[47]['home']?></li>
									<li class="ttsqd-r22"><?php echo empty($ttRes[47]['hid']) ? '1/8决赛' : $ttRes[47]['away']?></li>
									<li class="ttsqd-r23"><?php echo empty($ttRes[48]['hid']) ? '1/8决赛' : $ttRes[48]['home']?></li>
									<li class="ttsqd-r24"><?php echo empty($ttRes[48]['hid']) ? '1/8决赛' : $ttRes[48]['away']?></li>
									<li class="ttsqd-r1"><?php echo empty($ttRes[41]['hid']) ? 'C1' : $ttRes[41]['home']?></li>
									<li class="ttsqd-r2"><?php echo empty($ttRes[41]['hid']) ? 'A3/B3/F3' : $ttRes[41]['away']?></li>
									<li class="ttsqd-r3"><?php echo empty($ttRes[43]['hid']) ? 'E1' : $ttRes[43]['home']?></li>
									<li class="ttsqd-r4"><?php echo empty($ttRes[43]['hid']) ? 'D2' : $ttRes[43]['away']?></li>
									<li class="ttsqd-r5"><?php echo empty($ttRes[40]['hid']) ? 'A1' : $ttRes[40]['home']?></li>
									<li class="ttsqd-r6"><?php echo empty($ttRes[40]['hid']) ? 'C3/D3/E3' : $ttRes[40]['away']?></li>
									<li class="ttsqd-r7"><?php echo empty($ttRes[44]['hid']) ? 'B2' : $ttRes[44]['home']?></li>
									<li class="ttsqd-r8"><?php echo empty($ttRes[44]['hid']) ? 'F2' : $ttRes[44]['away']?></li>
								</ul>
							</div>
							<h3>淘汰赛前瞻：</h3>
							<p>从淘汰赛赛程粗略来看，B组，F组，D组第一将会在决赛前提前相遇，A组，C组，E组第一同样也是，根据分组情况看，英格兰，葡萄牙，西班牙很有可能在决赛前相遇，而另一半区法国，德国，比利时也极有可能会在决赛前相遇，因此选择决赛对阵玩法的彩友们，可以从上下半区各选择一支队，有效提高中奖率哦！</p>
						</div>
					</div>
				</div>
				<div class="euro-item euro-item2">
					<div class="wrap">
						<div class="euro-item-hd">
							<h2><i class="euro-item2-icom"></i>冠亚军彩玩法投注</h2>
							<div><i></i>猜猜谁能进入最后决赛，赢取巨额奖金！</div>
						</div>
						<div class="euro-bet-area">
							<a href="javascript:;" class="euro-bet-l">
								<div class="euro-bet-img">
									<img src="/caipiaoimg/v1.1/img/active/euro/img-putaoya.png" width="100" height="100" alt="">葡萄牙
								</div>
							</a>
							<a href="javascript:;" class="euro-bet-r">
								<div class="euro-bet-img">
									<img src="/caipiaoimg/v1.1/img/active/euro/img-faguo.png" width="100" height="100" alt="">法国
								</div>
							</a>
							<p class="euro-bet-note">比赛时间：7月11日 03:00<em>当前赔率：<b></b></em></p>
						</div>
					</div>
					<div class="euro-bet-bar-placeholder">
						<div class="euro-bet-bar">
							<div class="wrap">
								<p><span>您还未选择球队 </span></p>
								<div class="gyj-modifier">
									投注倍数
									<div class="multi-modifier">
			                            <a href="javascript:;" class="minus">-</a>
			                            <label><input class="multi number" type="text" value="1"></label>
			                            <a href="javascript:;" class="plus">+</a>
			                        </div>
									投注金额<em>0</em>元，预测奖金<em>0</em>元
								</div>
								<?php if($lotteryConfig[GYJ]['status']) {?>
									<a href="javascript:;" class="btn-bet-now submit <?php echo $showBind ? ' not-bind': '';?>" data-type="gyj">立即预约</a>
								<?php }else {?>
									<a href="javascript:;" class="btn-bet-now btn-disabled <?php echo $showBind ? ' not-bind': '';?>" data-type="gyj">暂停预约</a>
								<?php }?>
							</div>
						</div>
					</div>
					<div class="euro-finals-forecast">
						<div class="wrap">
							<hr>
							<h3>决赛对阵球队组合预测</h3>
							<div class="euro-finals-list">
								<ul>
								<?php for ($i = 0; $i < 4; $i++) {?>
									<li>
										<div class="euro-finals-table">
											<div class="euro-finals-head"><span>决赛球队</span><s>赔率</s></div>
											<div class="euro-finals-body">
											<?php foreach (array_slice($cmidList, $i * 13, 13) as $mid => $group) {
												$combine = $combineList[$group];
												$nameArr = explode('—', $combine['name']);?>
												<a href="javascript:;" class="<?php echo ($combine['status'] > 0) ? $ystaClass[$combine['status']] : ''?>" data-index="<?php echo $group?>" data-odd="<?php echo str_replace('.', '', $combine['odds'])?>" data-mid="<?php echo $combine['mid']?>" data-name0="<?php echo $nameArr[0]?>" data-name1="<?php echo $nameArr[1]?>" data-status="<?php echo $combine['status']?>">
													<span><?php echo $nameArr[0]?> - <?php echo $nameArr[1]?></span>
													<s><?php echo ($combine['status'] == 1) ? '淘汰' : $combine['odds']?></s>
													<i></i>
												</a>
											<?php }?>
											</div>
										</div>
									</li>
								<?php }?>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="euro-pop-mask" style="display:none"></div>
		<div class="euro-pop" style="display:none">
			<div class="euro-pop-hd">
				<a href="javascript:;" class="euro-pop-close">关闭</a>
			</div>
			<div class="euro-pop-bd">
				<div class="euro-pop-team item-logo">
					<ul>
					<?php foreach ($gjList as $group => $gjs){?>
						<li>
							<div class="euro-pop-title"><?php echo $group?>组</div>
							<div class="euro-pop-item">
							<?php foreach ($gjs as $mid => $gj) {?> 
								<a href="javascript:;" class="<?php echo $gj['logo']?> <?php if ($gj['status'] === '1') {?>euro-pop-eliminate<?php }elseif ($gj['status'] === '3') {?>euro-pop-suspend<?php }?>" data-logo="<?php echo $gj['logo']?>" data-index="<?php echo $mid?>" data-name="<?php echo $gj['name']?>">
								<i></i>
								<?php echo $gj['name']?>
								<b></b>
								</a>
								<?php }?>
							</div>
						</li>
					<?php }?>
					</ul>
				</div>
			</div>
			<div class="euro-pop-ft">
				<a href="javascript:;" class="euro-pop-btn">确定</a>
			</div>
		</div>
	<?php $this->load->view('v1.1/elements/common/footer_academy');?>
	</div>
</body>
<script>
var gjCodes = {}, gyjCodes = {}, gyjTmp = {}, gjBet = 0, gyjBet = 0, gjMulti = 1, gyjMulti = 1, gjoddmin = Number.POSITIVE_INFINITY, gjoddmax = 0, gyjoddmin = Number.POSITIVE_INFINITY, gyjoddmax = 0, gyjTmpLength = 0, tmpmid, combines = $.parseJSON('<?php echo json_encode($combineList)?>');
//冠军彩选中
$(".btn-ss").click(function(){
	if (!$(this).hasClass('btn-disabled')) {
		var tr = $(this).parents('.euro-group-tr'), codes;
		tr.toggleClass('selected');
		codes = {'name':tr.data('name'), 'odd':parseInt(tr.data('odd'), 10), mid:tr.data('index')};
		if (tr.hasClass('selected')) {
			gjCodes[tr.data('index')] = codes;
			gjBet += 1;
		}else {
			delete gjCodes[tr.data('index')];
			gjBet -= 1;
		}
		refreshGjOdd('gj');
		renderGj();
	}
})
function refreshGjOdd(type) {
	gjoddmin = Number.POSITIVE_INFINITY, gjoddmax = 0;
	for (i in gjCodes) {
		if (gjCodes[i].odd > gjoddmax) {
			gjoddmax = gjCodes[i].odd;
		}
		if (gjCodes[i].odd < gjoddmin) {
			gjoddmin = gjCodes[i].odd;
		}
	}
}
function pad(i) {
    i = '' + i;
    if (i.length < 2) {
        i = '0' + i;
    }
    return i;
}
var renderGj = function() {
	var gjstr = '', j = 0, oddstr = '', gjtt = '';
	for (i in gjCodes) {
		if (j < 5) {
			gjstr += gjCodes[i].name+'队、';
		}
		gjtt += gjCodes[i].name+'队、';
		j++;
	}
	gjstr = gjstr.slice(0, -1);
	gjtt = gjtt.slice(0, -1);
	if (j > 5) {
		gjstr += '...';
	}	
	if (gjoddmin == gjoddmax || gjoddmax == 0) {
		oddstr = gjoddmax * gjMulti/100 * 2;
	}else {
		oddstr = (gjoddmin * gjMulti/100 * 2)+'~'+(gjoddmax * gjMulti/100 * 2);
	}
	if (j == 0) {
		$(".gj-txt").html('您还尚未选择球队');
	}else {
		$(".gj-txt").html('您已选择'+gjstr+'夺冠');
		$(".gj-txt").attr('title', gjtt);
	}
	$('.gj-modifier em:first').html(gjBet * gjMulti * 2);
	$('.gj-modifier em:last').html(oddstr);
}
function castStr(type){
	var codestr = 'GJ|16001=';
	codeArr = [];
	for(i in gjCodes) {
		codeArr.push(pad(gjCodes[i].mid)+'('+gjCodes[i].odd/100+')');
	}
	codeArr.sort(function(a, b){
		amid = parseInt(a, 10);
		bmid = parseInt(b, 10);
		return amid > bmid ? 1 : ( amid < bmid ? -1 : 0 );
	})
	codestr += codeArr.join('/')+'|'+codeArr.length;
	return codestr;
}
$(".submit").click(function(){

	if (!$.cookie('name_ie')) {
    	cx.PopAjax.login(1);
        return ;
    }

    if ($(this).hasClass('not-bind')) {
        return ;
    }
	
	if (gjBet == 0) {
		cx.Alert({content:'请至少选择一场'});
		return;
	}
	var codeStr = castStr('gj');
	var betNum = gjBet;
	var lid = '44';
	var lotteryName = '冠军彩';
	var money = gjBet * gjMulti * 2;
	var multi = gjMulti;
	var postdata = {
		ctype: 'create',
        buyPlatform: 0,
        codes: codeStr,
        lid: lid,
        money: money,
        multi: multi,
        issue: '16001',
        playType: 0,
        betTnum: betNum,
        isChase: 0,
        orderType: 0,
        endTime: '2016-07-11 03:00:00'
	}
	cx.ajax.post({
        url: 'order/create',
        data: postdata,
        success: function(response){
        	if(response.code == 0) { 
            	var datas = {
                    ctype: 'pay',
                    orderId: response.data.orderId,
                    money: response.data.money
                };
            	var binfo = betInfo.jc( 
            		lotteryName, 
					response.data.money,
					response.data.remain_money
				);
                new cx.Confirm({
                    title: '确认投注信息',
                    content: binfo,
                    input: 0,
                    tip: '付款后，您的订单将会自动分配到空闲的投注站出票',
                    btns: [{type: 'confirm',txt: '付款到彩店',href: 'javascript:;'}],
                    confirmCb: function() {
                        cx.ajax.post({
                            url: 'order/pay',
                            data: datas,
                            success: function(response) {
                                castCb(response, lotteryName);
                            }
                        });
                    }
                });
            }else{
                castCb(response, lotteryName);
            }
        }
	})
})
var castCb = function (response, lotteryName) {
	if (response.code == 0) {
		new cx.Confirm({
			content: '<div class="mod-result result-success"><div class="mod-result-bd"><i class="icon-result"></i><div class="result-txt"><h2 class="result-txt-title">恭喜您，支付成功</h2><p>我们将尽快预约投注站出票</p></div></div></div>',
			btns: [{type: 'cancel',txt: '继续购彩'},{type: 'confirm',txt: '查看详情',href: baseUrl + 'orders/detail/' + response.data.orderId}],
			cancelCb: function () {location.reload();}
		});
	} else {
		if (response.code == 12) {
			new cx.Confirm({
				content: betInfo.jc(lotteryName, response.data.money, response.data.remain_money),
				btns: [{type: 'confirm',txt: '去充值',href: baseUrl + 'wallet/directPay?orderId=' + response.data.orderId}]
			});
		}else if(response.code == '3000'){
			new cx.Confirm({
				content: '<div class="mod-result result-success"><div class="mod-result-bd"><div class="result-txt"><h2 class="result-txt-title">您的登录已超时，请重新登录！</h2></div></div></div>',
				btns: [{type: 'confirm',txt: '重新登录',href: baseUrl + 'main/login'}]
			});
		} else {
			new cx.Alert({content: response.msg});
		}
	}
};
$(function(){
	var gjmultiModifier = new cx.AdderSubtractor('.gj-modifier .multi-modifier');
	var gyjmultiModifier = new cx.AdderSubtractor('.gyj-modifier .multi-modifier');
	gjmultiModifier.setCb(function(){
		gjMulti = parseInt(this.getValue(), 10);
		renderGj();
	})
	gyjmultiModifier.setCb(function(){
		gyjMulti = parseInt(this.getValue(), 10);
	})
	
	$('.euro-tab').on('click', 'li', function(){
		$(this).addClass('current').siblings().removeClass('current');
		$('.euro-con').find('.euro-item').eq($(this).index()).show().siblings().hide();
	})

	$('.euro-pop').on('click', '.euro-pop-close', function(){
		$(this).parents('.euro-pop').hide();
		$('.euro-pop-mask').hide();
	})


	var euroBarWrap = $('.euro-bet-bar-placeholder').eq(0);
    var euroBarWrap2 = $('.euro-bet-bar-placeholder').eq(1);
    var euroBar = euroBarWrap.find('.euro-bet-bar');
    var euroBar2 = euroBarWrap2.find('.euro-bet-bar');
    var euroBarHeight = euroBar.height();
    var castPanelTop = euroBarWrap.offset().top;
    var castPanelTop2;
    var baseTag = $('.euro-group-list').find('.euro-group-item').eq(0);
    var euroBarWrapTop = euroBarWrap.height() + euroBarWrap.offset().top;
    var baseTagTop = baseTag.offset().top + baseTag.height();
    var scrollTop, calcTopMin, calcTopMax;
    function onScroll() {
    	calcTopMin = baseTagTop - $(window).height() + euroBarHeight;
    	calcTopMax = euroBarWrapTop - $(window).height();
        scrollTop = $(document).scrollTop();

        if($('.euro-tab').find('li').eq(0).hasClass('current')){
        	if(scrollTop > calcTopMin && scrollTop < calcTopMax) {
        		euroBar.addClass('fixed');
	         } else {
	            euroBar.removeClass('fixed');
	        }
        }else{
        	castPanelTop2 = euroBarWrap2.offset().top;
        	if(scrollTop > castPanelTop2) {
	            euroBar2.addClass('fixed fixed2');
	         } else {
	            euroBar2.removeClass('fixed fixed2');
	        }
        }  
    }

    var timer;
    $(window).scroll(function () {
        onScroll();
    });
    $(window).resize(function(){
        clearTimeout(timer);
        timer = setTimeout(function(){
            onScroll();
        }, 100)
    });
    onScroll();
});
</script>

</html>