# Boxalino Data Integration for Magento2

## Introduction
The Boxalino Data Integration is based on a unified data structure. This is described in the public documentation
https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252280881/Data+Structure

This repository is to be used for the following data integration scopes:
1. Product
2. Order
3. Customers

## Setup
Consult with the [wiki page on Setup](https://github.com/boxalino/data-integration-magento2/wiki/Setup)

## Configuration
Consult with the [wiki page on Configurations](https://github.com/boxalino/data-integration-magento2/wiki/Configurations)

## Instant Update Integration
In case of an instant update data integration syncrhonization, the following documents are exported:
- [doc_product](https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252149870/doc_product),
- [doc_attribute_value](https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252313624/doc+attribute+value) (affected categories),
- [doc_language](https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252280975/doc+language)

Once the documents are available, the following requests are done:
* [load request](https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/415432770/Load+Request) per document
* a final [sync request](https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/394559761/Sync+Request)

For integration guidelines, review the [wiki instructions](https://github.com/boxalino/rtux-integration-magento2/wiki/)

## Full & Delta Integration
In case of a full data integration syncrhonization, the following documents are exported:
* for products:
    - [doc_product](https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252149870/doc_product),
    - [doc_attribute](https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252280945/doc+attribute),
    - [doc_attribute_value](https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252313624/doc+attribute+value),
    - [doc_language](https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252280975/doc+language)

* for orders:
    - [doc_order](https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252313666/doc_order)

* for customers:
    - [doc_user](https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252182638/doc+user)

For every data integration type (product, order, user), the following requests are done:
* [load request](https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/415432770/Load+Request) per document
* a final [sync request](https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/394559761/Sync+Request) per data integration type


## Extending the services
The repository provides a base plug-and-play logic for the data integration.
Any of the services provided are being used as part of the DocIntegrationHandler (per each doc type) as part of your project`s INTEGRATION LAYER.

**Boxalino is welcoming the community pull requests to extend the data integration elements**

## Contact us!

If you have any question, just contact us at support@boxalino.com
