# MapSeven.Neos.FormBundle

This Bundle contains various stuff to work with the Neos.Form Package in Neos CMS
## Installation
`composer require mapseven/neos-formbundle`

## Features
* Persistence Finisher - store submitted formData in a repository and/or ElasticSearch

## Usage
This Package contains only the Finishers and Helpers you can use in your Packages.
You'll find the Examples in the [FormBundleDemo](https://github.com/khuppenbauer/MapSeven.Neos.FormBundleDemo)

### Persistence Finisher
Just add the following finisher to the `form.yaml`

```yaml
#...
finishers:
  'MapSeven.Neos.FormBundle:Persistence':
    identifier: 'MapSeven.Neos.FormBundle:Persistence'
    options:
      db: true
      elasticSearch: false
```

The default options for the Persistence Finisher are those from the example above. Change them to your needs.

#### Override Configuration from NodeType Settings
By adding the options to the NodeTypes Configuration you can override the Options from the `form.yaml` with the NodeTypes Settings

Add the properties you want to override to the `NodeTypes.yaml`
```yaml
'MapSeven.Neos.FormBundleDemo:Form':
  superTypes:
    'Neos.NodeTypes:Form': true
  ui:
    label: Demo Form
    icon: 'icon-envelope-alt'
    inspector:
      groups:
        persistenceOptions:
          label: Persistence Options
          position: 40
  properties:
    #...
    db:
      type: boolean
      defaultValue: true
      ui:
        label: Repository
        inspector:
          group: persistenceOptions
    elasticSearch:
      type: boolean
      defaultValue: false
      ui:
        label: Elastic Search
        inspector:
          group: persistenceOptions
```

Override the Configuration with the Settings from the NodeType in the `form.html` Template
```html
{namespace formBundle=MapSeven\Neos\FormBundle\ViewHelpers}
<div{attributes -> f:format.raw()}>
    <f:if condition="{formIdentifier}">
        <f:then>
            <formBundle:render persistenceIdentifier="{formIdentifier}" presetName="{presetName}" overrideConfiguration="{finishers: {'MapSeven.Neos.FormBundle:Persistence': {options: {db: node.properties.db, elasticSearch: node.properties.elasticSearch}}}}" />
        </f:then>
        <f:else>
            <p>Please select a valid Form identifier in the inspector</p>
        </f:else>
    </f:if>
</div>
```
*Note: This example uses an adjusted form viewHelper, which unsets the finisher in case the `form.yaml` doesn't contain that finisher configuration*

#### ElasticSearch Mapping
This Package uses the [Flowpack/Elasticsearch Package](https://github.com/Flowpack/Flowpack.ElasticSearch) for Indexing FormData into ElasticSearch. That means you can use the Settings and other Features from that package. Have a look at the `Settings.yaml` for an example analyzer configuration I used for a facetted search.
Besides that you can define the mapping and transformers for each form item in a similar way in the `form.yaml`.

```yaml
    #...
    renderables:
      -
        type: 'Neos.Form:MultipleSelectCheckboxes'
        identifier: category
        label: 'Categories'
        properties:
          elementClassAttribute: 'checkbox'
          elementErrorClassAttribute: 'state-error'
          containerClassAttribute: ''
          options:
            cat1: Category 1
            cat2: Category 2
            cat3: Category 3
          elasticSearch:
            mapping:
              type: string
              analyzer: string_lowercase
              fields:
                raw:
                  type: string
                  index: not_analyzed
```

## License
Neos FormBundle is licensed under the [MIT Licence](LICENSE)

