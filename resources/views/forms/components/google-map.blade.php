@php
    $statePath = $getStatePath();
    $existingData = $getState() ?? [];
@endphp

<div x-data="{
    markers: [],
    map: null,
    searchBox: null,

    init() {
        // Initialize map
        this.map = new google.maps.Map(this.$refs.map, {
            center: {
                lat: {{ $existingData['latitude'] ?? 21.0285 }},
                lng: {{ $existingData['longitude'] ?? 105.8542 }}
            },
            zoom: 13
        });

        // Khởi tạo giá trị cho input nếu có data
        @if (isset($existingData['address'])) this.$refs.searchInput.value = '{{ $existingData['address'] }}'; @endif

        // Khởi tạo marker nếu có data
        @if (isset($existingData['latitude']) && isset($existingData['longitude'])) const savedMarker = new google.maps.Marker({
                map: this.map,
                position: {
                    lat: {{ $existingData['latitude'] }},
                    lng: {{ $existingData['longitude'] }}
                },
                draggable: true
            });
            
            this.markers.push(savedMarker);

            savedMarker.addListener('dragend', (event) => {
                const geocoder = new google.maps.Geocoder();
                
                geocoder.geocode(
                    { location: event.latLng },
                    (results, status) => {
                        if (status === 'OK' && results[0]) {
                            this.$refs.searchInput.value = results[0].formatted_address;
                            $wire.$set('{{ $statePath }}.latitude', event.latLng.lat());
                            $wire.$set('{{ $statePath }}.longitude', event.latLng.lng());
                            $wire.$set('{{ $statePath }}.address', results[0].formatted_address);
                        }
                    }
                );
            }); @endif

        // Initialize search box
        this.searchBox = new google.maps.places.SearchBox(this.$refs.searchInput);

        this.map.addListener('bounds_changed', () => {
            this.searchBox.setBounds(this.map.getBounds());
        });

        this.searchBox.addListener('places_changed', () => {
            const places = this.searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }

            this.markers.forEach((marker) => {
                marker.setMap(null);
            });
            this.markers = [];

            const bounds = new google.maps.LatLngBounds();

            places.forEach((place) => {
                if (!place.geometry || !place.geometry.location) {
                    console.log('Returned place contains no geometry');
                    return;
                }

                const marker = new google.maps.Marker({
                    map: this.map,
                    title: place.name,
                    position: place.geometry.location,
                    draggable: true
                });

                this.markers.push(marker);

                this.$refs.searchInput.value = place.formatted_address;
                $wire.$set('{{ $statePath }}.latitude', place.geometry.location.lat());
                $wire.$set('{{ $statePath }}.longitude', place.geometry.location.lng());
                $wire.$set('{{ $statePath }}.address', place.formatted_address);

                marker.addListener('dragend', (event) => {
                    const geocoder = new google.maps.Geocoder();

                    geocoder.geocode({ location: event.latLng },
                        (results, status) => {
                            if (status === 'OK' && results[0]) {
                                this.$refs.searchInput.value = results[0].formatted_address;
                                $wire.$set('{{ $statePath }}.latitude', event.latLng.lat());
                                $wire.$set('{{ $statePath }}.longitude', event.latLng.lng());
                                $wire.$set('{{ $statePath }}.address', results[0].formatted_address);
                            }
                        }
                    );
                });

                if (place.geometry.viewport) {
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });

            this.map.fitBounds(bounds);
        });
    }
}" x-init="init" wire:ignore class="space-y-4">
    <!-- Input search -->
    <div class="mb-4">

        {{-- <label style="display: block; ;margin-bottom: 10px; font-size: 14px" for="address-map">Address</label> --}}

        <input id="address-map" style="color: black" type="text" x-ref="searchInput"
            placeholder="Search for a location..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
    </div>

    <!-- Map container -->
    <div x-ref="map" style="height: 400px" class="w-full rounded-lg border border-gray-300 shadow-sm mt-4"></div>
</div>
