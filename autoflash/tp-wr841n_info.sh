#!/usr/bin/env sh

# attemps to access the status page of a TL-WR841N [1]
# and prints information:
#
#  - FW build version
#  - hardware version
#  - MAC
#
# [1]: using two known authentication methods, which as of
#      2015-03-28 should work with all FW versions

ROUTER_IP=192.168.0.1

curl_statuspage_http_auth () {
    curl --silent \
         --user admin:admin \
         --referer http://${ROUTER_IP}/ \
         "${ROUTER_IP}/userRpm/StatusRpm.htm"
}

curl_statuspage_cookie () {
    COOKIE="Authorization=Basic%20YWRtaW46MjEyMzJmMjk3YTU3YTVhNzQzODk0YTBlNGE4MDFmYzM%3D"

    # get "secret" session string
    TPLINK_SESSION=$(curl --silent --cookie ${COOKIE} "${ROUTER_IP}/userRpm/LoginRpm.htm?Save=Save" | sed 's|.*/\([A-Z]*\)/.*|\1|' | head -n 1)

    curl --silent \
         --cookie ${COOKIE} \
         --referer "http://${ROUTER_IP}/${TPLINK_SESSION}/userRpm/MenuRpm.htm" \
         "${ROUTER_IP}/${TPLINK_SESSION}/userRpm/StatusRpm.htm"
}

grep_interesting_stuff () {
    grep -o --colour=never -E '^"[^"]*(Build|WR841N v.|..-..-..-..-..-..)[^"]*",$' | tr -d '",'
}

curl_statuspage_http_auth | grep_interesting_stuff
curl_statuspage_cookie    | grep_interesting_stuff
