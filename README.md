# SsoClientBundle

Bundle que trabaja en conjunto con el bundle (SsoServerBundle)[https://github.com/manuelj555/SsoServerBundle/] para permitir la autenticación de usuarios en multiples sitios con un solo login.

## Instalación

Ejecutar: `composer require manuelj555/sso-client-bundle`

Agregar al AppKernel:

```php
public function registerBundles()
{
    $bundles = array(
        ...
        new Ku\SsoClientBundle\KuSsoClientBundle(),
    );

    ...
}
```

Agregar al routing.yml:

```yaml
ku_sso_client:
    resource: "@KuSsoClientBundle/Resources/config/routing.yml"
    prefix: /sso
```
