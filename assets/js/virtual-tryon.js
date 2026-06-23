(function () {
  'use strict';
  const $ = (id) => document.getElementById(id);
  const video = $('cameraVideo'), canvas = $('tryonCanvas'), ctx = canvas.getContext('2d');
  const stage = $('cameraStage'), empty = $('cameraEmpty'), loading = $('cameraLoading');
  const buttons = {start:$('startCamera'), stop:$('stopCamera'), capture:$('captureBtn'), save:$('saveBtn'), download:$('downloadBtn'), share:$('shareBtn')};
  let stream = null, pose = null, running = false, processing = false, landmarks = null, selected = null, captured = null, dressImage = new Image(), showLandmarks = true, animationId = null;

  function toast(message, danger) { const el=$('appToast'); el.querySelector('.toast-body').textContent=message; el.classList.toggle('text-bg-danger',!!danger); el.classList.toggle('text-bg-dark',!danger); bootstrap.Toast.getOrCreateInstance(el).show(); }
  function csrf() { return {name:document.querySelector('meta[name="csrf-name"]').content, token:document.querySelector('meta[name="csrf-token"]').content}; }
  function setStatus(text, on) { $('cameraStatus').textContent=text; document.querySelector('.live-dot').classList.toggle('on',!!on); }
  function syncCanvas() { const box=stage.getBoundingClientRect(); const ratio=Math.min(devicePixelRatio||1,2); canvas.width=Math.round(box.width*ratio); canvas.height=Math.round(box.height*ratio); ctx.setTransform(ratio,0,0,ratio,0,0); return {width:box.width,height:box.height}; }
  function asset(path) { if (!path) return window.APP.baseUrl+'assets/images/dress-placeholder.svg'; if (/^https?:/.test(path)) return path; return window.APP.baseUrl+'assets/'+path.replace(/^assets\//,''); }
  function selectProduct(product, card) {
    selected=product; captured=null; buttons.save.disabled=true; buttons.download.disabled=true; buttons.share.disabled=true;
    document.querySelectorAll('.dress-card').forEach(x=>x.classList.remove('active')); if(card) card.classList.add('active');
    $('selectedPreview').src=asset(product.product_image); $('selectedName').textContent=product.name; $('selectedCategory').textContent=product.category; $('selectedPrice').textContent='₹'+Number(product.price).toLocaleString('en-IN',{maximumFractionDigits:0}); $('selectedDescription').textContent=product.description||'';
    dressImage=new Image(); dressImage.crossOrigin='anonymous'; dressImage.src=asset(product.virtual_tryon_image);
  }
  document.querySelectorAll('.dress-card').forEach(card=>{ card.addEventListener('click',()=>selectProduct(JSON.parse(card.dataset.product),card)); });
  const first=document.querySelector('.dress-card'); if(first) selectProduct(JSON.parse(first.dataset.product),first);

  async function startCamera() {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) return toast('Camera access is not supported by this browser.',true);
    loading.classList.remove('d-none'); empty.classList.add('d-none'); buttons.start.disabled=true;
    try {
      stream=await navigator.mediaDevices.getUserMedia({video:{facingMode:'user',width:{ideal:1280},height:{ideal:720}},audio:false});
      video.srcObject=stream; await video.play(); running=true; setStatus('Live fitting in progress',true);
      buttons.stop.disabled=false; buttons.capture.disabled=false; loading.classList.add('d-none'); initPose(); render();
    } catch (error) {
      loading.classList.add('d-none'); empty.classList.remove('d-none'); buttons.start.disabled=false;
      const denied=error.name==='NotAllowedError'; toast(denied?'Camera permission was denied. Allow access in your browser settings and try again.':'We could not start the camera. Check that another app is not using it.',true);
    }
  }
  function initPose() {
    if (pose || typeof Pose==='undefined') { if(typeof Pose==='undefined') toast('Pose detection could not load. Camera mode will continue without tracking.',true); return; }
    pose=new Pose({locateFile:file=>'https://cdn.jsdelivr.net/npm/@mediapipe/pose/'+file});
    pose.setOptions({modelComplexity:1,smoothLandmarks:true,enableSegmentation:false,minDetectionConfidence:.55,minTrackingConfidence:.55});
    pose.onResults(results=>{ landmarks=results.poseLandmarks||null; processing=false; });
  }
  function stopCamera() {
    running=false; cancelAnimationFrame(animationId); if(stream) stream.getTracks().forEach(track=>track.stop()); stream=null; video.srcObject=null; landmarks=null; processing=false;
    ctx.clearRect(0,0,canvas.width,canvas.height); empty.classList.remove('d-none'); buttons.start.disabled=false; buttons.stop.disabled=true; buttons.capture.disabled=true; setStatus('Camera is off',false);
  }
  function drawFrame(size) {
    ctx.clearRect(0,0,size.width,size.height); if(video.readyState<2)return;
    const vr=video.videoWidth/video.videoHeight, cr=size.width/size.height; let sx=0,sy=0,sw=video.videoWidth,sh=video.videoHeight;
    if(vr>cr){sw=video.videoHeight*cr;sx=(video.videoWidth-sw)/2}else{sh=video.videoWidth/cr;sy=(video.videoHeight-sh)/2}
    ctx.drawImage(video,sx,sy,sw,sh,0,0,size.width,size.height);
    if(landmarks){ drawDress(size,landmarks); if(showLandmarks && typeof drawConnectors==='function'){ctx.save();drawConnectors(ctx,landmarks,POSE_CONNECTIONS,{color:'rgba(255,255,255,.5)',lineWidth:2});drawLandmarks(ctx,landmarks,{color:'#a72c48',fillColor:'#fff',radius:2});ctx.restore();} }
  }
  function drawDress(size,lm) {
    if(!dressImage.complete||!dressImage.naturalWidth)return;
    const ls=lm[11],rs=lm[12],lh=lm[23],rh=lm[24]; if(!ls||!rs||!lh||!rh||Math.min(ls.visibility||0,rs.visibility||0,lh.visibility||0,rh.visibility||0)<.4)return;
    const shoulderX=((ls.x+rs.x)/2)*size.width, shoulderY=((ls.y+rs.y)/2)*size.height;
    const shoulderWidth=Math.hypot((ls.x-rs.x)*size.width,(ls.y-rs.y)*size.height), torsoHeight=Math.hypot((((lh.x+rh.x)-(ls.x+rs.x))/2)*size.width,(((lh.y+rh.y)-(ls.y+rs.y))/2)*size.height);
    const width=shoulderWidth*1.72, aspect=dressImage.naturalHeight/dressImage.naturalWidth, height=Math.min(width*aspect,torsoHeight*3.25), angle=Math.atan2((rs.y-ls.y)*size.height,(rs.x-ls.x)*size.width);
    ctx.save();ctx.translate(shoulderX,shoulderY-shoulderWidth*.18);ctx.rotate(angle);ctx.globalAlpha=.92;ctx.drawImage(dressImage,-width/2,-width*.07,width,height);ctx.restore();
  }
  function render() {
    if(!running)return; const size=syncCanvas(); drawFrame(size);
    if(pose&&!processing&&video.readyState>=2){processing=true;pose.send({image:video}).catch(()=>{processing=false;});}
    animationId=requestAnimationFrame(render);
  }
  function capture() { if(!running)return; drawFrame(syncCanvas()); captured=canvas.toDataURL('image/png'); buttons.save.disabled=false;buttons.download.disabled=false;buttons.share.disabled=false;toast('Look captured. Save, share, or download it.'); }
  function download() { if(!captured)capture(); const a=document.createElement('a');a.href=captured;a.download='maison-ai-'+Date.now()+'.png';a.click(); }
  async function save() {
    if(!window.APP.loggedIn){toast('Sign in to save this look to your history.',true);return;} if(!selected)return toast('Select a dress first.',true); if(!captured)capture();
    buttons.save.disabled=true; const token=csrf(), body=new FormData();body.append('product_id',selected.id);body.append('image',captured);body.append(token.name,token.token);
    try{const response=await fetch(window.APP.siteUrl+'/api/save-tryon',{method:'POST',body,credentials:'same-origin'});const data=await response.json();if(data.csrf)document.querySelector('meta[name="csrf-token"]').content=data.csrf;if(!response.ok)throw new Error(data.message);toast(data.message||'Look saved.');}catch(e){toast(e.message||'Could not save this look.',true);buttons.save.disabled=false;}
  }
  async function share(){if(!captured)capture();try{const blob=await(await fetch(captured)).blob();const file=new File([blob],'maison-ai-look.png',{type:'image/png'});if(navigator.canShare&&navigator.canShare({files:[file]})){await navigator.share({title:'My Maison AI look',text:'A look I tried in the Maison AI fitting room.',files:[file]});}else{download();toast('Sharing is unavailable here, so we downloaded your look.');}}catch(e){if(e.name!=='AbortError')toast('Sharing was cancelled or unavailable.',true);}}
  buttons.start.addEventListener('click',startCamera);buttons.stop.addEventListener('click',stopCamera);buttons.capture.addEventListener('click',capture);buttons.download.addEventListener('click',download);buttons.save.addEventListener('click',save);buttons.share.addEventListener('click',share);
  $('resetBtn').addEventListener('click',()=>{captured=null;landmarks=null;buttons.save.disabled=true;buttons.download.disabled=true;buttons.share.disabled=true;toast('Fitting reset.');});
  $('changeDressBtn').addEventListener('click',()=>document.querySelector('.product-rail').scrollIntoView({behavior:'smooth',block:'center'}));
  $('toggleLandmarks').addEventListener('click',function(){showLandmarks=!showLandmarks;this.classList.toggle('active',showLandmarks);});
  $('fullscreenBtn').addEventListener('click',()=>{if(document.fullscreenElement)document.exitFullscreen();else stage.requestFullscreen();});
  $('sizeForm').addEventListener('submit',async function(e){e.preventDefault();const body=new FormData(this),token=csrf();body.append(token.name,token.token);try{const r=await fetch(window.APP.siteUrl+'/api/size-recommendation',{method:'POST',body});const d=await r.json();if(!r.ok)throw new Error(d.message);const box=$('sizeResult');box.innerHTML='<span>Your recommended starting size</span><strong>'+d.data.size+'</strong><small>'+d.data.confidence+'</small>';box.classList.remove('d-none');}catch(err){toast(err.message,true);}});
  window.addEventListener('beforeunload',stopCamera); window.addEventListener('resize',()=>{if(running)syncCanvas();});
})();
