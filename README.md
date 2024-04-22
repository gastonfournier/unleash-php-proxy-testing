Before pulling unleash image you need to sign in to ghcr.io with a token that can read packages

`echo $CR_PAT | docker login ghcr.io -u USERNAME --password-stdin`

To run the sample:
- `docker compose up`

Then you can login to unleash at 4242 and check the metrics of the toggle created.

```mermaid
flowchart LR
    A[PHP Proxy SDK] -.->|waits for| B(Edge)
    B -->|Connects to| C(Unleash)
    D[setup] -->|initialize| C
    B -.->|waits for| D
    A -->|evaluates against| B
    D -.->|waits for| C
```

# What is happening?
1. Spin up unleash-server with a postgres DB
2. Run a setup.sh script to create a toggle, turn it on in development and generate a frontend token that later will be used by the php proxy 
3. After setting up unleash-server, spin up an edge instance connected to unleash
4. Run a php proxy that will connect to unleash edge and use the frontend token to authenticate to unleash-server. The php proxy uses a persistent cache so metrics are not lost when the php proxy container restarts. 

# What happens if metrics are not 25?
The proxy has a persistent cache, so if you run the php proxy again, it will not send any metrics that were not sent before.

Just execute: `docker compose up evaluate`
