import re
import socket
import uuid as uid


def get_ip_address():
    hostname = socket.gethostname()
    return socket.gethostbyname(hostname)


def get_mac_address():
    return ':'.join(re.findall('..', '%012x' % uid.getnode()))
