from datetime import datetime

project = 'Unsigned'
author = 'Anton Smirnov'
copyright = '{} {}'.format(datetime.now().year, author)
language = 'en'

extensions = ['sphinxcontrib.phpdomain']

html_title = project
html_theme = 'furo'
templates_path = ["_templates"]
