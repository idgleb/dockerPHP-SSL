# Paso a Paso para Subir una Imagen Docker a un Clúster de Kubernetes de Google Cloud

## Paso 1: Preparar la Imagen Docker

### Construir la Imagen Docker

Primero, debes construir la imagen Docker en tu máquina local. Navega en la terminal (consola) al directorio que contiene tu **Dockerfile** y ejecuta el siguiente comando:

```sh
docker build -t gcr.io/<PROJECT_ID>/<IMAGE_NAME>:<TAG> .
```

> **Nota**: El punto al final es obligatorio.

- **`<PROJECT_ID>`**: ID de tu proyecto en Google Cloud. Puedes encontrarlo [aqui](https://console.cloud.google.com/cloud-resource-manager?referrer=search&hl=es&organizationId=0&orgonly=true&supportedpurview=organizationId,folder,project).
- **`<IMAGE_NAME>`**: Cualquier nombre para tu imagen Docker.
- **`<TAG>`**: Etiqueta opcional (por ejemplo, `v1`).

**Ejemplo**:

```sh
docker build -t gcr.io/intense-vault-442413-d0/img-php-ssl:v1 .
```

### Autenticar Docker con Google Container Registry (GCR)

Para subir la imagen al Google Container Registry (GCR), primero necesitas autenticar Docker con Google Cloud. En la terminal, ejecuta el siguiente comando:

```sh
gcloud auth configure-docker
```

Esto configurará Docker para autenticar automáticamente los registros de Google Cloud.

## Paso 2: Subir la Imagen a Google Container Registry

### Subir la Imagen al Registro

Una vez autenticado, puedes subir la imagen al Google Container Registry:

```sh
docker push gcr.io/<PROJECT_ID>/<IMAGE_NAME>:<TAG>
```

**Ejemplo**:

```sh
docker push gcr.io/intense-vault-442413-d0/img-php-ssl:v1
```

Puedes verificar que la imagen se haya subido correctamente navegando a [Google Container Registry](https://console.cloud.google.com/gcr/images) en la consola de Google Cloud y seleccionando tu proyecto.

## Paso 3: Configurar el Clúster de Kubernetes

### Crear un Clúster de Kubernetes (si aún no lo tienes)

Puedes crear un clúster [aqui](https://console.cloud.google.com/kubernetes/list) o mediante la terminal:

```sh
gcloud container clusters create <CLUSTER_NAME> --zone <ZONE>
```

- **`<CLUSTER_NAME>`**: Nombre para tu clúster.
- **`<ZONE>`**: Zona (Región) donde se creará el clúster (por ejemplo, `us-central1-a`).

### Conectar con el Clúster de Kubernetes

Para administrar el clúster, necesitas configurarlo localmente:

```sh
gcloud container clusters get-credentials <CLUSTER_NAME> --zone <ZONE>
```

**Ejemplo**:

```sh
gcloud container clusters get-credentials docerpagina-cluster-1 --zone us-central1
```

Esto descargará las credenciales del clúster para poder interactuar con él mediante `kubectl`.

## Paso 4: Crear el Archivo de Despliegue para Kubernetes

### Crear un Archivo YAML de Despliegue

Necesitas un archivo de despliegue para definir cómo Kubernetes debería manejar tu imagen Docker. Crea un archivo llamado **`deployment.yaml`** con el siguiente contenido:

```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: php-ssl-app-deployment
  labels:
    app: php-ssl-app
spec:
  replicas: 1
  selector:
    matchLabels:
      app: php-ssl-app
  template:
    metadata:
      labels:
        app: php-ssl-app
    spec:
      containers:
      - name: php-ssl-cont-app
        image: gcr.io/<PROJECT_ID>/<IMAGE_NAME>:<TAG>
        ports:
        - containerPort: 80
        - containerPort: 443
```

Reemplaza **`<PROJECT_ID>`**, **`<IMAGE_NAME>`**, y **`<TAG>`** por los valores correspondientes.

## Paso 5: Aplicar el Despliegue en el Clúster de Kubernetes

### Aplicar el Despliegue

Aplica el archivo de despliegue para crear los pods y servicios:

```sh
kubectl apply -f deployment.yaml
```

Esto creará los recursos definidos en el archivo YAML, y Kubernetes empezará a manejar la imagen. Puedes ver este despliegue (en la lista de trabajos) [aquí](https://console.cloud.google.com/kubernetes/workload/).

### Crear un Servicio para Exponer la Aplicación

Para que tu aplicación esté accesible externamente, debes crear un servicio de tipo **LoadBalancer**. Guarda esto como **`service.yaml`**:

```yaml
apiVersion: v1
kind: Service
metadata:
  name: php-ssl-app-service
spec:
  type: LoadBalancer
  selector:
    app: php-ssl-app
  ports:
    - name: http
      protocol: TCP
      port: 80
      targetPort: 80
    - name: https
      protocol: TCP
      port: 443
      targetPort: 443
```

### Aplicar el Archivo del Servicio

```sh
kubectl apply -f service.yaml
```

## Paso 6: Verificar los Recursos Creados

Puedes verificar que los recursos estén corriendo:

```sh
kubectl get deployments
kubectl get services
```

Esto te dará la información de los pods y servicios creados. El servicio de tipo **LoadBalancer** debería tener una **EXTERNAL-IP** que puedes usar para acceder a la aplicación.

Puedes entrar a tu sitio así: `http://<EXTERNAL-IP>` o `https://<EXTERNAL-IP>`

También puedes [ver detalles (pods, servicios) de tu despliegue aquí](https://console.cloud.google.com/kubernetes/workload) seleccionando tu despliegue.

Para ver las IP addresses que asignan a tus servicios [puedes entrar aquí](https://console.cloud.google.com/networking/addresses/list).
En misma página puedes reservar IP estatico para tu projecto.

