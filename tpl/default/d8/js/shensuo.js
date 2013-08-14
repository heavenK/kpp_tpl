// JavaScript Document

function getElementsByClassName(className,id,tag){
tag = tag || "*";
if(id){
var id =  typeof id == "string" ? $(id) : id;
}else{
var id = document.body;
}
var els = id.getElementsByTagName(tag),arr = [];
for(var i=0,n=els.length;i<n;i++){
for(var j=0,k=els[i].className.split(" "),l=k.length;j<l;j++){
if(k[j]==className){
arr.push(els[i]);
break;
}
}
}
return arr;
};
function Slide(slideClass,slideBtn,slideCon,slideSpeed) {
this.oSlides = getElementsByClassName(slideClass);
this.oTimer = null;
this.slideBtn = slideBtn;
this.slideCon = slideCon;
this.slideSpeed = slideSpeed;
}
Slide.prototype = {
oTimer:null,
_init:function (){
this._slideEvent();
},
_slideEvent:function (){
var This = this;
for(var i = 0,n=This.oSlides.length;i<n;i++){
(function(n){
var oSlide = This.oSlides[n];
var oSlideBtn = getElementsByClassName(This.slideBtn,oSlide)[0];
var oSlideCon = getElementsByClassName(This.slideCon,oSlide)[0];
oSlideBtn.onclick = function (){
if(oSlideCon.style.display == "block" && This.oTimer == null){
This._slideClose(oSlideCon);
}else if(!(oSlideCon.style.display == "block" ) && This.oTimer == null){
This._slideOpen(oSlideCon);
}
}
})(i)
}
},
_slideOpen:function (slideCon){
var This = this;
slideCon.style.display = "block";
slideCon.style.height = "auto";
var slideHeight = slideCon.offsetHeight;
slideCon.style.height = 0 + "px";
This.oTimer = setInterval(function (){
if(slideCon.offsetHeight < slideHeight){
slideCon.style.height = slideCon.offsetHeight + 2 + "px";
}else{
clearInterval(This.oTimer);
This.oTimer = null;
}
},This.slideSpeed);
},
_slideClose:function (slideCon){
var This = this;
This.oTimer = setInterval(function (){
if(slideCon.offsetHeight <= 0){
clearInterval(This.oTimer);
slideCon.style.display = "none";
This.oTimer = null;
}else{
slideCon.style.height =slideCon.offsetHeight - 2 + "px";
}
},This.slideSpeed);
}
}