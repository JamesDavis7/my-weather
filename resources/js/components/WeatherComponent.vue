<template>
  <div class="container w-full">
    <div class="w-full my-12">
      <div class="text-center">
        <h1 class="text-5xl text-white font-bold">My Weather</h1>
        <p class="text-white text-lg my-2">Instantly access current weather info and forecast for your desired location.</p>
      </div>

      <div
        id="geocoder"
        class="shadow-sm"
      >
        <div class="p-2 absolute right-0 mt-1 mr-1">
          <font-awesome-icon
            icon="magnifying-glass"
            class="text-xl"
          />
        </div>
      </div> <!-- Search Input Area -->
      <div
        class="w-full"
        v-if="currentWeather.main"
      >
        <!-- Current -->
        <div class="grid lg:grid-cols-2 gap-x-2 ">
          <div class="text-white rounded-xl mt-2 w-full flex flex-col relative">
            <div class="glass-card absolute w-full h-full shadow-xl -z-10 p-4 rounded-xl"></div>
            <div class="p-4">
              <div class="font-semibold text-2xl text-center z-10">Current Weather</div>
              <div class="text-center text-2xl mx-auto mt-6">{{location.name}}</div>

              <div class="mx-auto flex flex-col justify-center p-2">
                <div class="md:flex-row flex-col flex items-center justify-center">
                  <img
                    :src="(`/assets/${weathers[currentWeather.main]}.svg`)"
                    alt="weatherIcon"
                    class="w-56 pr-2"
                  />
                  <div class="text-6xl mb-8 md:mb-0 font-extralight">
                    {{currentWeather.actual}}°C
                  </div>
                </div>

                <div class="text-center text-3xl">{{currentWeather.main}}</div>
              </div>

            </div>

          </div>

          <div class="text-white rounded-xl mt-2 w-full flex flex-col relative">
            <div class="glass-card absolute w-full h-full shadow-xl -z-10 p-4 rounded-xl"></div>
            <div class="p-4">
              <div class="text-2xl font-semibold text-center md:text-left">Further Information</div>
              <div class="text-center text-2xl mx-auto mt-6">Feels like {{currentWeather.feels_like}}°C
              </div>
              <div class="grid md:grid-cols-2 justify-center items-center gap-2">
                <div class="flex items-center justify-center">
                  <img
                    src="/assets/humidity.svg"
                    alt="humidityIcon"
                    class="w-32"
                  >
                  <div class="text-white text-4xl">{{currentWeather.humidity}}%</div>
                </div>
                <div class="flex items-center justify-center">
                  <img
                    src="/assets/up.svg"
                    alt="upIcon"
                    class="w-32"
                  >
                  <div
                    v-for="(day, index) in daily"
                    :key="day.dt"
                    v-if="index < 1"
                  >
                    <div class="text-white text-4xl">{{Math.round(day.temp.max)}}°C</div>
                  </div>
                </div>
                <div class="flex items-center justify-center">
                  <img
                    src="/assets/wind.svg"
                    alt="windIcon"
                    class="w-32"
                  >
                  <div class="text-white text-4xl">{{currentWeather.wind_speed}} km/h</div>
                </div>
                <div class="flex items-center justify-center">
                  <img
                    src="/assets/down.svg"
                    alt="downIcon"
                    class="w-32"
                  >
                  <div
                    v-for="(day, index) in daily"
                    :key="day.dt"
                    v-if="index < 1"
                  >
                    <div class="text-white text-4xl">{{Math.round(day.temp.min)}}°C</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Forecast -->
        <div class="
                  grid
                  grid-cols-1
                  xs:grid-cols-2
                  sm:grid-cols-3
                  lg:grid-cols-6
                  gap-x-2">
          <div
            v-for="(day, index) in daily"
            :key="day.dt"
            v-if="index < 6"
          >

            <div class="text-white rounded-xl mt-2 relative">
              <div class="glass-card absolute w-full h-full shadow-xl -z-10 p-4 rounded-xl"></div>
              <div class="p-4">
                <div class="text-center">
                  <div class="text-xl font-semibold mt-2">{{toDayOfWeek(day.dt)}}</div>
                  <div class="flex justify-center">
                    <img
                      :src="(`/assets/${weathers[day.weather[0].main]}.svg`)"
                      alt="weatherIcon"
                      class="w-32"
                    />
                  </div>

                  <div class="text-lg">
                    <div>{{day.weather[0].main}}</div>
                    <div class="mt-2">{{Math.round(day.temp.max)}}°C / {{Math.round(day.temp.min)}}°C</div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  mounted() {
    mapboxgl.accessToken =
      "pk.eyJ1IjoiamFtZXNtYXBib3g0NTY0NiIsImEiOiJjbDI5OW96YXAwMjBtM2RvNHNycGl2a3dsIn0.toNAakoiiAL39wh7SDkVXQ";
    const geocoder = new MapboxGeocoder({
      accessToken: mapboxgl.accessToken,
      types: "place",
    });

    geocoder.addTo("#geocoder");

    geocoder.on("result", (e) => {
      this.location.name = e.result.place_name;
      this.location.lat = e.result.center[1];
      this.location.lon = e.result.center[0];
    });
  },
  watch: {
    location: {
      handler() {
        this.fetchData();
      },
      deep: true,
    },
  },
  data() {
    return {
      currentWeather: {
        main: "",
        temp: "",
        feels_like: "",
        icon: "",
      },
      weathers: {
        Snow: "snowy",
        Clouds: "cloudy",
        Rain: "rainy",
        Clear: "sunny",
        Thunder: "thunder",
        Mist: "mist",
      },
      daily: [],
      location: {
        name: "",
        lat: "",
        lon: "",
      },
    };
  },
  methods: {
    fetchData() {
      fetch(`/api/weather?lat=${this.location.lat}&lon=${this.location.lon}`)
        .then((res) => res.json())
        .then((data) => {
          this.currentWeather.actual = Math.round(data.current.temp);
          this.currentWeather.feels_like = Math.round(data.current.feels_like);
          this.currentWeather.main = data.current.weather[0].main;
          this.currentWeather.humidity = data.current.humidity;
          this.currentWeather.wind_speed = data.current.wind_speed;
          this.daily = data.daily;
        });
    },
    toDayOfWeek(timestamp) {
      const toHumanFormat = new Date(timestamp * 1000);
      const days = [
        "Sunday",
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
      ];
      return days[toHumanFormat.getDay()];
    },
  },
};
</script>

