#!/bin/ash

# Instalar tzdata
apk add --no-cache tzdata

# Configurar o fuso horário para America/Cuiaba
ln -sf /usr/share/zoneinfo/America/Cuiaba /etc/localtime

# Sincronizar o relógio com o host
hwclock -s
