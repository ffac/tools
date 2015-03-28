#!/usr/bin/env sh

# TP-Link Autoflasher for TL-WR841N v9 with firmware 3.16.9
#
# only use this if you understand what you are doing
# there are no safeguards against flashing bad images
#
# usage: ./autoflash.sh /full/path/to/firmware-image.bin
#
# alternatively, set a fixed path below

FIRMWARE_PATH=$1
#FIRMWARE_PATH=/Users/macbook/Downloads/gluon-ffac-2014.4-stable-01-tp-link-tl-wr841n-nd-v9.bin

ROUTER_IP=192.168.0.1

# this should not need to be changed, unless TP-Link once again
# thinks of a "clever" new security mechanism
COOKIE="Authorization=Basic%20YWRtaW46MjEyMzJmMjk3YTU3YTVhNzQzODk0YTBlNGE4MDFmYzM%3D"

# get "secret" session string
TPLINK_SESSION=$(curl --silent --cookie ${COOKIE} "${ROUTER_IP}/userRpm/LoginRpm.htm?Save=Save" | sed 's|.*/\([A-Z]*\)/.*|\1|' | head -n 1)
echo "Logged in, Session string: ${TPLINK_SESSION}"

# initiate FW update
echo "About to post firmware"
curl --cookie ${COOKIE} \
     --referer "http://${ROUTER_IP}/${TPLINK_SESSION}/userRpm/SoftwareUpgradeRpm.htm" \
     "${ROUTER_IP}/${TPLINK_SESSION}/incoming/Firmware.htm" \
     --form "Filename=@${FIRMWARE_PATH}" \
     --form "Upgrade=Upgrade" | grep --silent "system reboots" && \
     echo "Firmware sent. Router should flash and reboot. DO NOT DISCONNECT POWER!" && \
     exit 0

echo "Something unexpected happened; upload probably failed"
exit 1
