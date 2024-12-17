// Function to get the user's current location
export const getCurrentLocation = (): Promise<{ latitude: number; longitude: number }> =>
    new Promise((resolve, reject) => {
        if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          (position) => {
            resolve(
                {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                }
            );
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
    