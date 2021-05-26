Airtable Client Bundle (Work In Progress)
==================

[![Static code analysis](https://github.com/yoanbernabeu/Airtable-Client-Bundle/actions/workflows/main.yml/badge.svg)](https://github.com/yoanbernabeu/Airtable-Client-Bundle/actions/workflows/main.yml)

The Airtable Client bundle is a Symfony bundle that attempts to make the Airtable API easier to use.

- Retrieve data from a table, optionally choosing a view
- Retrieve a recording by ID

## Installation

Airtable Client Bundle requires PHP 7.4+ and Symfony 5.2+.

Install this bundle using Composer and Symfony Flex:

```sh
composer require yoanbernabeu/airtable-client-bundle
```

To configure Airtable Client Bundle, please create an **airtable_client.yaml** file in **config/packages/** with the following information:

```yaml
airtable_client:
  key:
  id:
```

To find your **Airtable base ID**, go to your **API documentation** and look in the **Introduction** section.

![Airtable ID](docs/airtable_id.png)

To find your **Airtable API key**, go to your **Account options** and search in the **API** section.

![Airtable KEY](docs/airtable_key.png)

## Usage

```php
// ...
use Yoanbernabeu\AirtableClientBundle\AirtableClient;

class foo
{
    public function bar(AirtableClient $airtableClient)
    {
        // ...
        $airtableClient->findAll('tableName', 'viewName');
        $airtableClient->findOneById('tableName', 'id');
        $airtableClient->findBy('tableName', 'fieldName', 'value');
        // ...
    }

    // ...
}
```

## License

See the bundled [LICENSE](https://github.com/yoanbernabeu/Airtable-Client-Bundle/blob/main/LICENCE) file.