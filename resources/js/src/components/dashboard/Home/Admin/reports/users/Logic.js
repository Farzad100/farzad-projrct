import m from 'moment-jalaali';
m.loadPersian({
  dialect: 'persian-modern'
});

import { api, wrapper } from '@/global/services';

export default {
  name: 'UsersReports',

  data() {
    return {
      td: [],

      pie: {
        series: [],
        options: {
          chart: {
            fontFamily: 'Pelak, sans-serif',
          },
          labels: [
            'ارگانیک',
            'تبلیغات',
          ],
          plotOptions: {
            pie: {
              donut: {
                labels: {
                  show: true,
                  total: {
                    label: 'مجموع',
                    show: true,
                    formatter: function (w) {
                      return w.globals.seriesTotals.reduce((a, b) => {
                        return a + b;
                      }, 0).toLocaleString();
                    }
                  }
                }
              }
            }
          },
        },
      },

      area: {
        series: [],
        options: {
          chart: {
            height: 350,
            type: 'line',
            fontFamily: 'Pelak, sans-serif',
            toolbar: {
              show: true,
              tools: {
                download: '<i class="fal fa-caret-square-down"></i>',
                selection: false,
                zoom: '<i class="fal fa-search"></i>',
                zoomin: false,
                zoomout: false,
                pan: '<i class="fal fa-arrows-alt"></i>',
                reset: '<i class="fal fa-home"></i>',
                customIcons: []
              },
              autoSelected: 'zoom' 
            },
          },
          noData: {
            text: 'در حال بارگذاری...'
          },
          stroke: {
            curve: 'smooth'
          },
          tooltip: {
            y: {
              formatter: function(value) {
                return `${value.toLocaleString()}`;
              }
            },
            x: {
              show: true,
              formatter: function (value) {
                let dt = m(value).format('YYYY-MM-DD HH:mm:ss');
                return `${m(dt).format('dddd')} - ${m(value).format('LL')}`;
              },
            },
          },
          xaxis: {
            type: 'datetime',
            labels: {
              formatter: function (value) {
                return m(value).format('LL');
              }, 
            },
            axisBorder: {
              show: true,
              color: 'rgba(0, 87, 159, 0.05)',
            },
          },
          yaxis: {
            labels: {
              offsetX: '-20px',
              formatter: function (value) {
                return value.toLocaleString();
              },
            },
            axisBorder: {
              show: true,
              color: 'rgba(0, 87, 159, 0.05)',
              offsetX: 14,
            },
          },
          grid: {
            show: true,
            strokeDashArray: 0,
            borderColor: 'rgba(0, 87, 159, 0.05)',
            position: 'back',
            xaxis: {
              lines: {
                show: true
              }
            },   
            yaxis: {
              lines: {
                show: true
              }
            },  
          },
          legend: {
            position: 'top',
            horizontalAlign: 'right', 
          }
        },
      },

      lineFilter: '1',
      filterShow: false,

      dateFilter: {
        from_date: '',
        to_date: ''
      }
    };
  },

  watch: {
    lineFilter(val) {
      if (val) {
        this.loadLine(val);
      }
    }
  },

  created() {
    this.loadCharts();
  },

  methods: {
    async loadLine(payload, filter) {
      // HTTP Request
      const { data } = await wrapper(api.Admin.charts.users.line(payload, filter));

      // Response
      if (data) {
        const { series, colors } = data.result;

        let total = {
          name: 'مجموع',
          data: []
        };
          
        for (let i in series[0].data) {
          total.data.push(
            [
              series[0].data[i][0],
              parseInt([series[0].data[i][1]]) + parseInt([series[1].data[i][1]])
            ]
          );
        }

        let newSeries = [
          ...series,
          total
        ];

        this.$refs.chart.updateSeries(newSeries);
        this.$refs.chart.updateOptions({ colors });
      }
    },

    async loadPie() {
      // HTTP Request
      const { data } = await wrapper(api.Admin.charts.users.pie());

      // Response
      if (data) {
        const { series, colors } = data.result;

        let seriesData = [];

        for (let i of series) {
          seriesData.push(i.data);
        }
      
        this.pie.series = seriesData;
        this.$refs.pie.updateOptions({ colors });
      }
    },

    async loadOverview() {
      const { data } = await wrapper(api.Admin.charts.users.overview());

      if (data) {
        const { result } = data;
        this.td = result;
      }
    },

    filterDate() {
      this.lineFilter = null;
      const query = `from_date=${this.dateFilter.from_date}&to_date=${this.dateFilter.to_date}`;
      this.loadLine(null, query);
    },

    loadCharts() {
      this.loadLine();
      this.loadPie();
      this.loadOverview();
    }
  }
};