<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<meta name="format-detection" content="email=no,telephone=no,address=no">

	<title></title>
	<meta name="description" content="">

	<!-- OGP -->
	<meta property="og:type" content="article">
	<meta property="og:description" content="">
	<meta property="og:title" content="">
	<meta property="og:site_name" content="">
	<meta property="og:url" content="">
	<meta property="og:image" content="">
	<meta property="og:locale" content="ja_JP">
	<meta name="twitter:card" content="summary_large_image">
	<!-- <meta name="twitter:site" content="@"> -->

	<!-- favicon -->
	<link rel="shortcut icon" href="/favicon.ico">
	<meta name="theme-color" content="#5fbccb">

	<!-- CSS -->
	<style>
		* {
			border: none;
			box-sizing: border-box;
			margin: 0;
			padding: 0;
			outline: none;
		}
		html {
			font-size: 10px;
		}
		body {
			background: #fff;
			color: #111;
			font-size: 1.6rem;
			font-family: "Noto Serif JP", "游ゴシック Medium", "Yu Gothic Medium", 游ゴシック, "Yu Gothic", 游ゴシック体, YuGothic, "ヒラギノ角ゴ Pro W3", "Hiragino Kaku Gothic Pro", "メイリオ", Meiryo, Osaka, sans-serif;
			letter-spacing: 0.05em;
			line-height: 2;
			font-weight: 300;
			-webkit-text-size-adjust: 100%;
			overflow-wrap: break-word;
			word-wrap: break-word;
		}
		main {
			align-items: center;
			display: flex;
			justify-content: center;
			width: 100%;
			height: 100vh;
			position: relative;
		}
		.l_wrap {

		}
		canvas {
			background: #f3f3f3;
			display: block;
			margin: 0 auto;
		}
		form {
			position: absolute;
			bottom: 80px;
			left: 80px;
			z-index: 1;
		}
		form dl dt {
			font-weight: 900;
		}
		form dl dd {
			display: flex;
			flex-wrap: wrap;
			justify-content: flex-start;
		}
		form dl dd input {
			border-radius: 5px;
			display: block;
			font-weight: 900;
			max-width: 65px;
			margin: 0 10px;
			padding: 10px 20px;
		}
	</style>

	<!-- JS -->
	<link rel="preconnect" href="https://fonts.googleapis.com/">
	<link rel="preconnect" href="https://ajax.googleapis.com/">
</head>

<body>
	<header role="banner">

	</header>
	<main role="main">
		<div class="l_wrap">
			<canvas id="mapCanvas"></canvas>
			<form action="" method="post">
				<dl>
					<dt>座標</dt>
					<dd>
						<input type="text" value="W">
						<input type="text" value="0">°
						<input type="text" value="0">'
						<input type="text" value="0">"
						<input type="text" value="S">
						<input type="text" value="0">°
						<input type="text" value="0">'
						<input type="text" value="0">"
						<button type="submit">検索</button>
					</dd>
				</dl>
			</form>
		</div>
	</main>
	<footer role="footer">

	</footer>

<!-- webfont -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;900&display=swap">

<!-- JS -->
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js" defer></script> -->
<script async defer>
	var canvas = document.getElementById('mapCanvas');
	canvas.width = document.body.clientWidth - 100;
	canvas.height = window.innerHeight - 100;
	var wc = canvas.width / 2;
	var hc = canvas.height / 2;

	var img = new Image;

	window.onload = function(){
		var ctx = canvas.getContext('2d');
		ctx.strokeStyle = '#fff';
		ctx.fillStyle = '#f00';
		ctx.lineWidth = 10;

		trackTransforms(ctx);
		function redraw(){
			var p1 = ctx.transformedPoint(0,0);
			var p2 = ctx.transformedPoint(canvas.width,canvas.height);
			ctx.clearRect(p1.x,p1.y,p2.x-p1.x,p2.y-p1.y);
			ctx.drawImage(img,wc-iwc,hc-ihc);
			ctx.save();
			ctx.translate(-2.5,0);
			ctx.beginPath();
			ctx.arc(wc,hc, 5,0, Math.PI*2);
			ctx.stroke();
			ctx.fill();
			ctx.restore();
		}
		redraw();

		var lastX = wc;
		var lastY = hc;
		var dragStart,dragged;
		canvas.addEventListener('mousedown',function(evt){
			document.body.style.mozUserSelect = document.body.style.webkitUserSelect = document.body.style.userSelect = 'none';
			lastX = evt.offsetX || (evt.pageX - canvas.offsetLeft);
			lastY = evt.offsetY || (evt.pageY - canvas.offsetTop);
			dragStart = ctx.transformedPoint(lastX,lastY);
			dragged = false;
		},false);
		canvas.addEventListener('mousemove',function(evt){
			lastX = evt.offsetX || (evt.pageX - canvas.offsetLeft);
			lastY = evt.offsetY || (evt.pageY - canvas.offsetTop);
			dragged = true;
			if (dragStart){
				var pt = ctx.transformedPoint(lastX,lastY);
				ctx.translate(pt.x-dragStart.x,pt.y-dragStart.y);
				redraw();
			}
		},false);
		canvas.addEventListener('mouseup',function(evt){
			dragStart = null;
			if (!dragged) zoom(evt.shiftKey ? -1 : 1 );
		},false);

		var scaleFactor = 1.1;
		var zoom = function(clicks){
			var pt = ctx.transformedPoint(lastX,lastY);
			ctx.translate(pt.x,pt.y);
			var factor = Math.pow(scaleFactor,clicks);
			ctx.scale(factor,factor);
			ctx.translate(-pt.x,-pt.y);
			redraw();
		}

		var handleScroll = function(evt){
			var delta = evt.wheelDelta ? evt.wheelDelta/40 : evt.detail ? -evt.detail : 0;
			if (delta) zoom(delta);
			return evt.preventDefault() && false;
		};
		canvas.addEventListener('DOMMouseScroll',handleScroll,false);
		canvas.addEventListener('mousewheel',handleScroll,false);
	};
	img.src = 'map.jpg';
	// var iwc = img.width / 2;
	// var ihc = img.height / 2;
	var iwc = 5967 / 2;
	var ihc = 4997 / 2;

	function trackTransforms(ctx){
		var svg = document.createElementNS("http://www.w3.org/2000/svg",'svg');
		var xform = svg.createSVGMatrix();
		ctx.getTransform = function(){ return xform; };
		
		var savedTransforms = [];
		var save = ctx.save;
		ctx.save = function(){
			savedTransforms.push(xform.translate(0,0));
			return save.call(ctx);
		};
		var restore = ctx.restore;
		ctx.restore = function(){
			xform = savedTransforms.pop();
			return restore.call(ctx);
		};

		var scale = ctx.scale;
		ctx.scale = function(sx,sy){
			xform = xform.scaleNonUniform(sx,sy);
			return scale.call(ctx,sx,sy);
		};
		var rotate = ctx.rotate;
		ctx.rotate = function(radians){
			xform = xform.rotate(radians*180/Math.PI);
			return rotate.call(ctx,radians);
		};
		var translate = ctx.translate;
		ctx.translate = function(dx,dy){
			xform = xform.translate(dx,dy);
			return translate.call(ctx,dx,dy);
		};
		var transform = ctx.transform;
		ctx.transform = function(a,b,c,d,e,f){
			var m2 = svg.createSVGMatrix();
			m2.a=a; m2.b=b; m2.c=c; m2.d=d; m2.e=e; m2.f=f;
			xform = xform.multiply(m2);
			return transform.call(ctx,a,b,c,d,e,f);
		};
		var setTransform = ctx.setTransform;
		ctx.setTransform = function(a,b,c,d,e,f){
			xform.a = a;
			xform.b = b;
			xform.c = c;
			xform.d = d;
			xform.e = e;
			xform.f = f;
			return setTransform.call(ctx,a,b,c,d,e,f);
		};
		var pt  = svg.createSVGPoint();
		ctx.transformedPoint = function(x,y){
			pt.x=x; pt.y=y;
			return pt.matrixTransform(xform.inverse());
		}
	}
</script>

</body>
</html>