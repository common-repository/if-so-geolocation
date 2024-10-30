
(function( $ ) {
    $(document).ready(function () {
        checkForLocationRequests();
        document.addEventListener('ifso_ajax_triggers_loaded',checkForLocationRequests);
    })

    function checkForLocationRequests(){
        if(typeof(ifso_scope.getCookie)==='undefined') return;
        var getLocationTags = $('IfsoGetBrowserLocation');
        if(getLocationTags.length>0){
            for(tag of getLocationTags){
                if(tag.getAttribute('ignore_cache')==='true' || (!hasAnyOverrideData()) ){
                    getBrowserGeolocation();
                    return;
                }
            }
        }
        if(ifso_scope.getCookie(requestBrowserLocCookieName)!==null && !hasAnyOverrideData()){
            getBrowserGeolocation();
            ifso_scope.createCookie(requestBrowserLocCookieName,'',-1);
            return;
        }

    }

    function getBrowserGeolocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(rememberPosition);
        }
    }

    function hasAnyOverrideData(){
        return (hasGeoOverrideData(geoOverrideCookieName) || hasGeoOverrideData(browserLocationCookieName));
    }

    function hasGeoOverrideData(cname){
        if(ifso_scope.getCookie(cname)){
            try{
                var geoOverrideObj = JSON.parse(ifso_scope.getCookie(cname));
                //if(typeof(geoOverrideObj.lat)!=='undefined' && typeof(geoOverrideObj.lat)!=='undefined')
                return true;
            }
            catch {
                return false;
            }
        }
        return false;
    }

    function rememberPosition(position) {
        var coordsString = position.coords.latitude + ',' + position.coords.longitude;
        var geoObject = {lat:position.coords.latitude,long:position.coords.longitude};
        ifso_scope.createCookie(browserLocationCookieName,JSON.stringify(geoObject),0);
        window.location.reload();
    }
})(jQuery);

