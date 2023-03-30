import base64
import PIL.Image as Image
import io
from image.gege import gege


def generate_image():
    b = base64.b64decode(gege)
    img = Image.open(io.BytesIO(b))
    img.show()
    