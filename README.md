# OpenCart v4.1.0.0 Theme creator 2025
Theme creator for OpenCart v4.1.0.0 to:

## How it works
Generates an extension: copy template files from `./files` in new temporary folder, replaces in the file content and in file names:
- %name% with $name;
- %theme_name% with $theme_name, `$theme_name = 'oc_' . preg_replace('/^oc[\_]*/ig', '', $name)`;
- %ThemeName% with PascalCase of $theme_name;
- %version% with $version;
- %author_name% with $author_name;
- %author_link% with $author_link;
archive to `$theme_name.ocmod.zip` in the $output_dir | $output_file.;

## How to use it

1. Configure your theme: name, output_dir, output_file, version, author_name, author_link.
```bash
Usage:
php ./theme-creator.php [name] [out_dir|out_file] [-a author] [-u url] [-v version]
    name     - The name of the theme (Theme or theme_ prefixes omitting).
    out_dir  - The path to the system/storage/marketplace directory or another one if you wish.
    out_file - The path to the archived ${theme_name}.ocmod.zip file if ends up with .zip.
    author   - Name of the theme's author, default is "Weblegko".
    url      - Link to the website of the theme's author, default is https://weblegko.ru/.
    version  - The version, default is 1.0.
```
![Terminal](./docs/1-terminal.png)

2. Open Admin area and go to Extensions >> Installer and find a created theme recently.
![Extensions Installer](./docs/02-extensions-installer.png)

3. Add it to the system.
![Extensions](./docs/03-extensions-extensions.png)

4. Go to Extensions >> Extensions >> Choose the extension type [Themes] and add your theme.
![Themes](./docs/04-extensions-themes.png)

5. Activate your theme.
![Activate Extensions](./docs/05-extensions-activate.png)
