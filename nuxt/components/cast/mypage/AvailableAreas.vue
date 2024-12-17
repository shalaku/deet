<script setup lang="ts">
import haversine from 'haversine-distance';

const { data } = defineProps<{
  data: string[];
}>();

// Area listing with predefined coordinates and a distance property
type Area = {
  name: string;
  latitude: number;
  longitude: number;
  distance: string | null; 
};

const areaListing: Area[] = [
  { name: 'Bar V / 六本木', latitude: 35.66283, longitude: 139.73175, distance: null },
];

const env_param = process.env.NODE_ENV;

let currentLocation = ref<{ latitude: number; longitude: number } | null>(null);

// Function to get the user's current location
const getCurrentLocation = (): Promise<{ latitude: number; longitude: number }> =>
  new Promise((resolve, reject) => {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          resolve({
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
          });
        },
        (error) => {
          console.error('Error getting location:', error);
          reject(error);
        }
      );
    } else {
      reject(new Error('Geolocation is not supported by this browser.'));
    }
  });

// Compute distances between the current location and predefined locations
const computeDistances = async () => {
  try {
    const location = await getCurrentLocation();
    currentLocation.value = location;

    areaListing.forEach((area) => {
      const point1 = { latitude: area.latitude, longitude: area.longitude };
      const point2 = { latitude: location.latitude, longitude: location.longitude };

      const distance = haversine(point1, point2) / 1000; // Convert meters to kilometers
      area.distance = distance.toFixed(2); 
    });
  } catch (error) {
    console.error('Error computing distances:', error);
  }
};

// Compute distances on component mount
computeDistances();
</script>

<template lang="html">
  <hr />
  <div class="content-body region-container">
    <h3 class="mb-4 center-align-on-mobile text-start fs-3">対応可能地域</h3>
		<div class="region-list">
			<div class="d-flex justify-content-lg-start justify-content-md-center align-items-start mb-3 flex-wrap fs-4">
			<!-- <div class="row justify-content-start justify-content-md-between gap-2"> -->
				<div
					v-for="(area, index) in areaListing"
					:key="index"
					class="tag me-3 mb-3 fs-4 p-4"
					:class="{
						'available-item': area === data?.find((item) => item === area),
					}"
				>
					{{ area.name }}
				</div>
			</div>
		</div>
    <!-- Current location section -->
    <div class="current-location-info mb-4" v-if="env_param === 'development'">
      <h4>Your Current Location:</h4>
      <div v-if="currentLocation" class="d-flex justify-content-between">
        <p><strong>Latitude:</strong> {{ currentLocation.latitude }}</p>
        <p><strong>Longitude:</strong> {{ currentLocation.longitude }}</p>
      </div>
      <div v-else>
        <p>Fetching location...</p>
      </div>
    </div>

    <!-- Available areas section -->
    <div class="available-areas-info" v-if="env_param === 'development'">
      <h4>Available Areas:</h4>
      <div
        v-for="(area, index) in areaListing"
        :key="index"
        class="area-item d-flex justify-content-between mb-3"
      >
        <p><strong>Name:</strong> {{ area.name }}</p>
        <p><strong>Latitude:</strong> {{ area.latitude }}</p>
        <p><strong>Longitude:</strong> {{ area.longitude }}</p>
        <p><strong>Distance:</strong> {{ area.distance }} km</p>
      </div>
    </div>
  </div>
</template>

<style scoped>

.content-body {
  font-family: Arial, sans-serif;
}

/* Current location info */
.current-location-info,
.available-areas-info {
  background: black;
  padding: 1rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  margin-bottom: 1rem;
}

.d-flex {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}

.justify-content-between {
  justify-content: space-between;
}

.area-item {
  padding: 0.5rem 0;
  border-bottom: 1px solid #ddd;
}

.area-item:last-child {
  border-bottom: none;
}
</style>

