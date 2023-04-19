<template>
    <div class="w-100">
        <canvas :id="id" :height="height" :width="width"></canvas>
    </div>
</template>

<script>
    export default {
        data() {
          return {
              testData : {
                  labels: ["Africa", "Asia", "Europe", "Latin America", "North America"],
                  datasets: [
                      {
                          backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850"],
                          data: [2478,5267,734,784,433]
                      }
                  ]
              }
          }
        },
        props: ["height", "id", "title", "data", "type", "width"],
        mounted() {
            let app = this;
            let type = this.$props.type ==  null ? 'bar' : this.$props.type;
            let graph = new Chart(document.getElementById(this.$props.id), {
                type: type,
                data: this.$props.data == null ? app.testData : this.$props.data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    },
                    legend: {
                        display: type !== "bar",
                    },
                    title: {
                        display: this.$props.title != null,
                        text: this.$props.title
                    }
                }
            });
            graph.update();
        }
    }
</script>
