var smdWrapper = document.createElement("script"), smdScript;
smdWrapper.async = true;
smdWrapper.type = "text/javascript";
smdWrapper.src = "https://cpx.smind.si/Log/LogData?data=" + JSON.stringify({
  Key: cenejeVars.shopId,
  Size: "80",
  Type: "badge",
  BadgeClassName: "smdWrapperTag",
  Version: 2
});
smdScript = document.getElementsByTagName("script")[0];
smdScript.parentNode.insertBefore(smdWrapper, smdScript);