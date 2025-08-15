# act testing framework

## Configuration via act.xml

You can define the paths for steps and scenarios via an `act.xml` file at the project root.

Example file:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<act>
    <paths>
        <steps>tests/steps</steps>
        <scenarios>tests/scenarios</scenarios>
    </paths>
</act>
```

Default paths if `act.xml` is missing or incomplete:

- steps: `tests/steps`
- scenarios: `tests/scenarios`

## Command line

Run the tool with a specific configuration file:

```bash
php bin/act.php --configuration=act.xml
```

Supported aliases:

- `--configuration`, `-c`