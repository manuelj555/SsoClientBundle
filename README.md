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

Luego en el config.yml configurar el bundle:

```yaml
ku_sso_client:
    api_key: debe ser una clave secreta # Clave compartida entre server y cliente para transmisión de datos
    sso:
        login:          http://app_domain.com/sso/login # url en el server donde se inicia la sesión sso del usuario.
        logout:         http://app_domain.com/sso/logout # url en el server donde se termina la sesión sso del usuario.
        authentication: http://app_domain.com/sso/authenticate # url en el server donde se genera la autenticación sso.
```

Por último se debe añadir la configuración para el firewall en el security.yml:

```yaml
firewalls:
    # ...
    main:
        pattern: ^/
        entry_point: ku_sso_client.security.entry_point
        simple_preauth:
            authenticator: ku_sso_client.security.otp_authenticator
```

Con esto la aplicación estará asegurada y cuando un usuario intente acceder, se verificará que haya iniciado sesión en la aplicación principal (servidor), y se creará el token de autenticación en la aplicación cliente.
