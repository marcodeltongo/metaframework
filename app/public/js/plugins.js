/* Place any jQuery/helper plugins in here, instead of separate, slower script files. */

// http://patik.com/blog/complete-cross-browser-console-log/ - Saved from GitHub on April 13, 2011.
if(Function.prototype.bind&&console&&typeof console.log=="object"){["log","info","warn","error","assert","dir","clear","profile","profileEnd"].forEach(function(method){console[method]=this.call(console[method],console);},Function.prototype.bind);}
if(!window.log){window.log=function(){log.history=log.history||[];log.history.push(arguments);if(typeof console!='undefined'&&typeof console.log=='function'){if(window.opera){var i=0;while(i<arguments.length){console.log("Item "+(i+1)+": "+arguments[i]);i++;}}
else if((Array.prototype.slice.call(arguments)).length==1&&typeof Array.prototype.slice.call(arguments)[0]=='string'){console.log((Array.prototype.slice.call(arguments)).toString());}
else{console.log(Array.prototype.slice.call(arguments));}}
else if(!Function.prototype.bind&&typeof console!='undefined'&&typeof console.log=='object'){Function.prototype.call.call(console.log,console,Array.prototype.slice.call(arguments));}
else{if(!document.getElementById('firebug-lite')){var script=document.createElement('script');script.type="text/javascript";script.id='firebug-lite';script.src='https://getfirebug.com/firebug-lite.js';document.getElementsByTagName('HEAD')[0].appendChild(script);setTimeout(function(){log(Array.prototype.slice.call(arguments));},2000);}
else{setTimeout(function(){log(Array.prototype.slice.call(arguments));},500);}}}}
