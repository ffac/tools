#!/usr/bin/env sh

# TP-Link Autoflasher for TL-WR841N v9 with firmware 3.14.4
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

curl_upload () {
    curl --user admin:admin \
         --referer "http://${ROUTER_IP}/" \
         "http://${ROUTER_IP}/incoming/Firmware.htm" \
         --form "Filename=@${FIRMWARE_PATH}" \
         --form "Upgrade=Upgrade"
}

curl_confirm () {
     curl --silent --show-error \
          --user admin:admin \
          --referer "http://${ROUTER_IP}/" \
          "http://${ROUTER_IP}/userRpm/FirmwareUpdateTemp.htm"
}

# initiate FW update
# in contrast to the most recent FW version, with this one it is apparently
# not enough to just upload the FW, but a sort of confirmation page needs to
# be visited

echo "About to post firmware"
curl_upload  | grep --silent "system reboots" && \
echo "Please wait for this command to finish (30s is normal) ..." && \
curl_confirm | grep --silent "Completed!"     && \
echo "Firmware sent. Router should flash and reboot. DO NOT DISCONNECT POWER!" && \
exit 0

echo "Something unexpected happened; upload probably failed"
exit 1
