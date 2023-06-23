### k3s install
-  curl -sfL https://rancher-mirror.rancher.cn/k3s/k3s-install.sh | INSTALL_K3S_MIRROR=cn sh -s - --docker

- 查看节点
- kubectl get node
- 查看pod
- k3s kubectl get pods --all-namespaces

```sh

// 创建应用
kubectl create deployment nginx --image=nginx --port=80 --replicas=2
// 创建访问
kubectl create service clusterip nginx --tcp=80:80
// 查看
kubectl describe deployment nginx
kubectl describe service nginx
// 查看容器
crictl ps
or
docker ps

// only create yaml
kubectl create service clusterip nginx --tcp=80:80 --dry-run=client -o yaml > nginx_service.yaml
kubectl create deployment nginx --image=nginx --port=80 --replicas=2 --dry-run=client -o yaml > nginx.yaml

// del
kubectl delete deployment nginx
kubectl delete service nginx

//add replicas
kubectl scale --replicas=3 deployment/nginx
```

### k3s 配置mysql。
- configmap

```yaml
### 文件名 mysql-configmap.yaml
apiVersion: v1
kind: ConfigMap
metadata:
  name: mysql-db-config
  namespace: default
  labels:
    app: mysql-db-config
data:
  my.cnf: |
    [client]
    default-character-set=utf8mb4
    [mysql]
    default-character-set=utf8mb4
    [mysqld]
    character-set-server = utf8mb4
    collation-server = utf8mb4_unicode_ci
    init_connect='SET NAMES utf8mb4'
    skip-character-set-client-handshake = true
    max_connections=2000
    secure_file_priv=/var/lib/mysql
    datadir=/var/lib/mysql
    bind-address=0.0.0.0
    symbolic-links=0
```
- 应用
- kubectl apply -f mysql-singleton/mysql-configmap.yaml

- create service and deployment

```yaml
### 文件名 deployment-service.yaml
apiVersion: v1
kind: Service
metadata:
  name: mysql
spec:
  type: NodePort
  ports:
    - port: 3306
      nodePort: 30306
      targetPort: mysql
  selector:
    app: mysql
---
apiVersion: apps/v1
kind: Deployment
metadata:
  name: mysql
spec:
  selector:
    matchLabels:
      app: mysql
  strategy:
    type: Recreate
  template:
    metadata:
      labels:
        app: mysql
    spec:
      containers:
        - image: mysql:8.0
          name: mysql
          env:
            - name: MYSQL_ROOT_PASSWORD
              value: root123
          ports:
            - containerPort: 3306
              name: mysql
          volumeMounts:
            - name: mysql-persistent-storage
              mountPath: /var/lib/mysql
            - name: mysql-config
              mountPath: /etc/mysql/conf.d/my.cnf
              subPath: my.cnf
      volumes:
        - name: mysql-persistent-storage
          emptyDir: {}
        - name: mysql-config
          configMap:
            name: mysql-db-config
            items:
              - key: my.cnf
                path: my.cnf
```

- kubectl apply -f mysql-singleton/deployment-service.yaml



### k3s 配置redis。
- configmap

```yaml
### 文件名 redis-configmap.yaml
apiVersion: v1
kind: ConfigMap
metadata:
  name: redis-config
data:
  redis.conf: |
    requirepass 123456
    protected-mode no
    port 6379
    tcp-backlog 511
    timeout 0
    tcp-keepalive 300
    daemonize no
    supervised no
    pidfile /var/run/redis_6379.pid
    loglevel notice
    logfile ""
    databases 16
    always-show-logo yes
    save 900 1
    save 300 10
    save 60 10000
    stop-writes-on-bgsave-error yes
    rdbcompression yes
    rdbchecksum yes
    dbfilename dump.rdb
    dir /data
    slave-serve-stale-data yes
    slave-read-only yes
    repl-diskless-sync no
    repl-diskless-sync-delay 5
    repl-disable-tcp-nodelay no
    slave-priority 100
    lazyfree-lazy-eviction no
    lazyfree-lazy-expire no
    lazyfree-lazy-server-del no
    slave-lazy-flush no
    appendonly no
    appendfilename "appendonly.aof"
    appendfsync everysec
    no-appendfsync-on-rewrite no
    auto-aof-rewrite-percentage 100
    auto-aof-rewrite-min-size 64mb
    aof-load-truncated yes
    aof-use-rdb-preamble no
    lua-time-limit 5000
    slowlog-log-slower-than 10000
    slowlog-max-len 128
    latency-monitor-threshold 0
    notify-keyspace-events Ex
    hash-max-ziplist-entries 512
    hash-max-ziplist-value 64
    list-max-ziplist-size -2
    list-compress-depth 0
    set-max-intset-entries 512
    zset-max-ziplist-entries 128
    zset-max-ziplist-value 64
    hll-sparse-max-bytes 3000
    activerehashing yes
    client-output-buffer-limit normal 0 0 0
    client-output-buffer-limit slave 256mb 64mb 60
    client-output-buffer-limit pubsub 32mb 8mb 60
    hz 10
    aof-rewrite-incremental-fsync yes
```

- create service and deployment


```yaml
### 文件名 deployment-service.yaml
---
apiVersion: apps/v1
kind: Deployment
metadata:
  name: redis-singleton # Unique name for the deployment
  labels:
    app: redis       # Labels to be applied to this deployment
spec:
  selector:
    matchLabels:     # This deployment applies to the Pods matching these labels
      app: redis
      role: master
      tier: backend
  replicas: 1        # Run a single pod in the deployment
  template:          # Template for the pods that will be created by this deployment
    metadata:
      labels:        # Labels to be applied to the Pods in this deployment
        app: redis
        role: master
        tier: backend
    spec:            # Spec for the container which will be run inside the Pod.
      containers:
        - name: redis-singleton
          image: redis
          resources:
            requests:
              cpu: 100m
              memory: 100Mi
#          command: ["redis-server","/etc/redis/redis.conf"]
          command:
            - redis-server
            - /etc/redis/redis.conf
          ports:
            - containerPort: 6379
          volumeMounts:
            - name: redis-config
              mountPath: /etc/redis/redis.conf
              subPath: redis.conf
            - name: redis-storage
              mountPath: /data
      volumes:
        - name: redis-storage
          emptyDir: {}
        - name: redis-config
          configMap:
            name: redis-config
            items:
              - key: redis.conf
                path: redis.conf
---
apiVersion: v1
kind: Service        # Type of Kubernetes resource
metadata:
  name: redis-svc # Name of the Kubernetes resource
  labels:            # Labels that will be applied to this resource
    app: redis
    role: master
    tier: backend
spec:
  type: NodePort
  ports:
    - port: 6379       # Map incoming connections on port 6379 to the target port 6379 of the Pod
      targetPort: 6379
      nodePort: 31379
  selector:          # Map any Pod with the specified labels to this service
    app: redis
    role: master
    tier: backend
```