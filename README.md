Paso a Paso para Subir una Imagen Docker a un Clúster de Kubernetes de Google Cloud
Paso 1: Preparar la Imagen Docker
Construir la Imagen Docker:
Primero, debes construir la imagen Docker en tu máquina local. Navega en terminal(consola) al directorio que contiene tu Dockerfile y ejecuta el siguiente comando:
docker build -t gcr.io/<PROJECT_ID>/<IMAGE_NAME>:<TAG> .  (el punto es obligatorio)

<PROJECT_ID>: ID de tu proyecto en Google Cloud. https://console.cloud.google.com/cloud-resource-manager?referrer=search&hl=es&organizationId=0&orgonly=true&supportedpurview=organizationId,folder,project

<IMAGE_NAME>: Cualquier nombre de la imagen Docker.

<TAG>: Etiqueta opcional (por ejemplo, v1).

En mi caso asi: docker build -t gcr.io/intense-vault-442413-d0/img-php-ssl:v1 .
Autenticar Docker con Google Container Registry (GCR):
Para subir la imagen al Google Container Registry (GCR), primero necesitas autenticar Docker con Google Cloud. En terminal ejecuta este:
gcloud auth configure-docker
Esto configurará Docker para autenticar automáticamente los registros de Google Cloud.
Paso 2: Subir la Imagen a Google Container Registry
Subir la Imagen al Registro:
Una vez autenticado, puedes subir la imagen al Google Container Registry:
docker push gcr.io/<PROJECT_ID>/<IMAGE_NAME>:<TAG>
En mi caso asi: docker push gcr.io/intense-vault-442413-d0/img-php-ssl:v1

Puedes verificar que la imagen se haya subido correctamente navegando a Google Container Registry en la consola de Google Cloud https://console.cloud.google.com/gcr/images (y seleccione su projecto)
Paso 3: Configurar el Clúster de Kubernetes
Crear un Clúster de Kubernetes (si aún no lo tienes):
Puedes crearlo aqui: https://console.cloud.google.com/kubernetes/list
O através de terminal:
gcloud container clusters create <CLUSTER_NAME> --zone <ZONE>
<CLUSTER_NAME>: Nombre para tu clúster.
<ZONE>: Zona(Region) donde se creará el clúster (por ejemplo, us-central1-a).

Conectar con el Clúster de Kubernetes:
Para administrar el clúster, necesitas configurarlo:
gcloud container clusters get-credentials <CLUSTER_NAME> --zone <Region>

En mi caso asi: gcloud container clusters get-credentials docerpagina-cluster-1 --zone us-central1
Esto descargará las credenciales del clúster para poder interactuar con él mediante kubectl.
Paso 4: Crear el Archivo de Despliegue para Kubernetes
Crear un Archivo YAML de Despliegue:
Necesitas un archivo de despliegue para definir cómo Kubernetes debería manejar tu imagen Docker. Crea un archivo llamado deployment.yaml con el siguiente contenido:
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



Reemplaza <PROJECT_ID>, <IMAGE_NAME>, y <TAG> por los valores correspondientes.
Paso 5: Aplicar el Despliegue en el Clúster de Kubernetes
Aplicar el Despliegue:
Aplica el archivo de despliegue para crear los pods y servicios:
kubectl apply -f deployment.yaml
Esto creará los recursos definidos en el archivo YAML, y Kubernetes empezará a manejar la imagen.
Puedes ver este deploiment(en la lista de trabajos) aca https://console.cloud.google.com/kubernetes/workload/
Crear un Servicio para Exponer la Aplicación:
Para que tu aplicación esté accesible externamente, debes crear un servicio de tipo LoadBalancer(Guarda esto como service.yaml):
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



Aplica el archivo:
kubectl apply -f service.yaml
3. Verificar los Recursos Creados:
   Puedes verificar que los recursos estén corriendo:
   kubectl get deployments
   kubectl get services
   Esto te dará la información de los pods y servicios creados. El servicio de tipo LoadBalancer debería tener una EXTERNAL-IP que puedes usar para acceder a la aplicación.
   Y puedes entrar a tu sitio así: http://<EXTERNAL-IP>, https://<EXTERNAL-IP>
   Tambien puedes ver detalles(pods, sevicios) de tu deployment aqui: https://console.cloud.google.com/kubernetes/workload
   elige tu deployment(trabajo)
