var HT = HT || {};
HT.login_status = {};
HT.login_status.logged_in = true;
HT.login_status.institutionName = decodeURIComponent('HathiTrust');
HT.login_status.institutionCode = decodeURIComponent('hathitrust');
HT.login_status.mappedInstitutionName = decodeURIComponent('');
HT.login_status.mappedInstitutionCode = decodeURIComponent('');
HT.login_status.affiliation = decodeURIComponent('Member');
HT.login_status.providerName = decodeURIComponent('HathiTrust');
HT.login_status.expired = false;
HT.login_status.authType = "shibboleth";
HT.login_status.message = "LOGGED IN";
HT.login_status.u = false;
HT.login_status.x = false;
HT.login_status.idp_list = null;
HT.login_status.r = JSON.parse('{"totalAccess":false}');
HT.login_status.notificationData = [];
HT.analyticsSettings = {};
HT.hotjarSettings = {};
HT.shibboleth_alert = function() {
    alert("Please quit your browser to log out of Shibboleth.");
    return false;
}

HT.get_pong_target = function(target) {
    return "https://localhost/cgi/ping/pong?target=" + target;
}

HT.login_href = function() {
    if ( HT.login_status.logged_in ) {
        if ( HT.login_status.authType == 'shibboleth' ) {
            return "";
        } else {
            return "https://localhost/cgi/logout?" + window.location.href;
        }
    }
    // not logged in
    var target_1 = encodeURIComponent("https://localhost/cgi/ping/pong?target=");
    return "http://localhost/cgi/wayf?target=" + target_1 + encodeURIComponent(window.location.href);
}

HT.login_link = function() {
    var href = HT.login_href();
    var link;
    if ( HT.login_status.logged_in ) {
        link = HT.login_status.affiliation;
        if ( HT.login_status.providerName ) {
            link += " (" + HT.login_status.providerName + ")";
        }
        link += ' <a href="https://localhost/cgi/logout" id="loginLink">(logout)</a>';
    } else {
        link = '<a href="' + href + '">Login</a>';
    }

    return link;
}

ping_handler(HT.login_status);
