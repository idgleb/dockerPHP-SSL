<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Desplegar Imagen Docker en Google Kubernetes Engine</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h1 class="text-center mb-4">Desplegar Imagen Docker en Google Kubernetes Engine (GKE)</h1>

    <div class="accordion" id="deploymentSteps">
        <div class="accordion-item">
            <h2 class="accordion-header" id="stepOneHeader">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#stepOne" aria-expanded="true" aria-controls="stepOne">
                    Paso 1: Preparar la Imagen Docker
                </button>
            </h2>
            <div id="stepOne" class="accordion-collapse collapse show" aria-labelledby="stepOneHeader" data-bs-parent="#deploymentSteps">
                <div class="accordion-body">
                    <h5>Construir la Imagen Docker:</h5>
                    <p>Primero, necesitas construir la imagen Docker en tu máquina local. Navega al directorio que contiene tu <code>Dockerfile</code> y ejecuta el siguiente comando en tu terminal (consola):</p>
                    <pre><code>docker build -t gcr.io/&lt;PROJECT_ID&gt;/&lt;IMAGE_NAME&gt;:&lt;TAG&gt; .</code></pre>
                    <p><strong>Nota:</strong> El punto al final es obligatorio.</p>
                    <ul>
                        <li><code>&lt;PROJECT_ID&gt;</code>: ID de tu proyecto en Google Cloud. Encuéntralo <a href="https://console.cloud.google.com/cloud-resource-manager?referrer=search&hl=es&organizationId=0&orgonly=true&supportedpurview=organizationId,folder,project" target="_blank">aquí</a>.</li>
                        <li><code>&lt;IMAGE_NAME&gt;</code>: Cualquier nombre para tu imagen Docker.</li>
                        <li><code>&lt;TAG&gt;</code>: Etiqueta opcional (por ejemplo, <code>v1</code>).</li>
                    </ul>
                    <p>Ejemplo:</p>
                    <pre><code>docker build -t gcr.io/intense-vault-442413-d0/img-php-ssl:v1 .</code></pre>
                    <h5>Autenticar Docker con Google Container Registry (GCR):</h5>
                    <p>Para subir la imagen a GCR, necesitas autenticar Docker con Google Cloud. Ejecuta el siguiente comando:</p>
                    <pre><code>gcloud auth configure-docker</code></pre>
                    <p>Esto configurará Docker para autenticar automáticamente los registros de Google Cloud.</p>
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header" id="stepTwoHeader">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stepTwo" aria-expanded="false" aria-controls="stepTwo">
                    Paso 2: Subir la Imagen a Google Container Registry
                </button>
            </h2>
            <div id="stepTwo" class="accordion-collapse collapse" aria-labelledby="stepTwoHeader" data-bs-parent="#deploymentSteps">
                <div class="accordion-body">
                    <h5>Subir la Imagen al Registro:</h5>
                    <p>Una vez autenticado, puedes subir la imagen a GCR usando el siguiente comando:</p>
                    <pre><code>docker push gcr.io/&lt;PROJECT_ID&gt;/&lt;IMAGE_NAME&gt;:&lt;TAG&gt;</code></pre>
                    <p>Ejemplo:</p>
                    <pre><code>docker push gcr.io/intense-vault-442413-d0/img-php-ssl:v1</code></pre>
                    <h5>Verificar la Imagen en Google Container Registry:</h5>
                    <p>Puedes verificar que la imagen se haya subido correctamente navegando a <a href="https://console.cloud.google.com/gcr/images" target="_blank">Google Container Registry</a> y seleccionando tu proyecto.</p>
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header" id="stepThreeHeader">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stepThree" aria-expanded="false" aria-controls="stepThree">
                    Paso 3: Configurar el Clúster de Kubernetes
                </button>
            </h2>
            <div id="stepThree" class="accordion-collapse collapse" aria-labelledby="stepThreeHeader" data-bs-parent="#deploymentSteps">
                <div class="accordion-body">
                    <h5>Crear un Clúster de Kubernetes (si aún no lo tienes):</h5>
                    <p>Puedes crear un clúster <a href="https://console.cloud.google.com/kubernetes/list" target="_blank">aquí</a> o a través de la terminal:</p>
                    <pre><code>gcloud container clusters create &lt;CLUSTER_NAME&gt; --zone &lt;ZONE&gt;</code></pre>
                    <ul>
                        <li><code>&lt;CLUSTER_NAME&gt;</code>: Nombre para tu clúster.</li>
                        <li><code>&lt;ZONE&gt;</code>: Región donde se creará el clúster (por ejemplo, <code>us-central1-a</code>).</li>
                    </ul>
                    <h5>Conectar con el Clúster de Kubernetes:</h5>
                    <p>Para administrar el clúster, necesitas configurarlo localmente:</p>
                    <pre><code>gcloud container clusters get-credentials &lt;CLUSTER_NAME&gt; --zone &lt;ZONE&gt;</code></pre>
                    <p>Ejemplo:</p>
                    <pre><code>gcloud container clusters get-credentials docerpagina-cluster-1 --zone us-central1</code></pre>
                    <p>Este comando descarga las credenciales del clúster para interactuar con él usando <code>kubectl</code>.</p>
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header" id="stepFourHeader">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stepFour" aria-expanded="false" aria-controls="stepFour">
                    Paso 4: Crear el Archivo de Despliegue para Kubernetes
                </button>
            </h2>
            <div id="stepFour" class="accordion-collapse collapse" aria-labelledby="stepFourHeader" data-bs-parent="#deploymentSteps">
                <div class="accordion-body">
                    <h5>Crear un Archivo YAML de Despliegue:</h5>
                    <p>Crea un archivo llamado <code>deployment.yaml</code> con el siguiente contenido:</p>
                    <pre><code>apiVersion: apps/v1
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
        image: gcr.io/&lt;PROJECT_ID&gt;/&lt;IMAGE_NAME&gt;:&lt;TAG&gt;
        ports:
        - containerPort: 80
        - containerPort: 443</code></pre>
                    <p>Reemplaza <code>&lt;PROJECT_ID&gt;</code>, <code>&lt;IMAGE_NAME&gt;</code>, y <code>&lt;TAG&gt;</code> con tus valores respectivos.</p>
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header" id="stepFiveHeader">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stepFive" aria-expanded="false" aria-controls="stepFive">
                    Paso 5: Aplicar el Despliegue en Kubernetes
                </button>
            </h2>
            <div id="stepFive" class="accordion-collapse collapse" aria-labelledby="stepFiveHeader" data-bs-parent="#deploymentSteps">
                <div class="accordion-body">
                    <h5>Aplicar el Despliegue:</h5>
                    <p>Aplica el archivo de despliegue para crear los pods y servicios:</p>
                    <pre><code>kubectl apply -f deployment.yaml</code></pre>
                    <p>Puedes ver el despliegue (en la lista de cargas de trabajo) <a href="https://console.cloud.google.com/kubernetes/workload/" target="_blank">aquí</a>.</p>
                    <h5>Crear un Servicio para Exponer la Aplicación:</h5>
                    <p>Para que tu aplicación esté accesible externamente, crea un servicio de tipo LoadBalancer. Guarda esto como <code>service.yaml</code>:</p>
                    <pre><code>apiVersion: v1
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
      targetPort: 443</code></pre>
                    <h5>Aplicar el Archivo del Servicio:</h5>
                    <pre><code>kubectl apply -f service.yaml</code></pre>
                    <h5>Verificar los Recursos Creados:</h5>
                    <p>Ejecuta los siguientes comandos para verificar que los recursos estén ejecutándose:</p>
                    <pre><code>kubectl get deployments
kubectl get services</code></pre>
                    <p>El servicio LoadBalancer debería tener una <code>EXTERNAL-IP</code> que puedes usar para acceder a la aplicación:</p>
                    <pre><code>http://&lt;EXTERNAL-IP&gt; o https://&lt;EXTERNAL-IP&gt;</code></pre>
                    <p>También puedes ver detalles (pods, servicios) de tu despliegue <a href="https://console.cloud.google.com/kubernetes/workload/" target="_blank">aquí</a> seleccionando tu despliegue.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
