<template>
    <OwnerLayout>
        <div
            class="min-h-screen py-8"
            style="
                background: linear-gradient(135deg, #535862 0%, #3a3f4a 100%);
            "
        >
            <div
                class="glass-card-dark p-6 rounded-lg shadow-glow max-w-4xl mx-auto border border-white/20"
            >
                <h1 class="text-3xl font-bold text-white mb-6">
                    Add New Vehicle
                </h1>

                <!-- Stepper / Progress -->
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 mr-4">
                            <div
                                class="w-full bg-white/10 rounded-full h-2 overflow-hidden"
                            >
                                <div
                                    class="bg-blue-500 h-2 transition-all duration-300"
                                    :style="{
                                        width: `${Math.round(
                                            ((currentStep + 1) / steps.length) *
                                                100
                                        )}%`,
                                    }"
                                ></div>
                            </div>
                        </div>
                        <div
                            class="text-white/80 text-sm min-w-[130px] text-right"
                        >
                            Step {{ currentStep + 1 }} of {{ steps.length }}
                        </div>
                    </div>
                    <div class="mt-3 grid grid-cols-3 sm:grid-cols-7 gap-2">
                        <button
                            v-for="(s, idx) in steps"
                            :key="s.key"
                            type="button"
                            class="flex items-center gap-2 px-2 py-1 rounded-md border transition-colors w-full justify-start"
                            :class="
                                (() => {
                                    const visited = idx < currentStep;
                                    const current = idx === currentStep;
                                    const valid = stepValidityList[idx];
                                    if (current)
                                        return [
                                            isCurrentStepValid
                                                ? 'bg-blue-500/20 border-blue-400/40 text-white'
                                                : 'bg-blue-500/10 border-red-400/60 text-white',
                                            '',
                                        ];
                                    if (visited && valid)
                                        return 'bg-emerald-500/15 border-emerald-400/30 text-emerald-200';
                                    if (visited && !valid)
                                        return 'bg-red-500/10 border-red-400/50 text-red-200';
                                    return 'bg-white/5 border-white/10 text-white/70';
                                })()
                            "
                            :disabled="!canNavigateTo(idx)"
                            :title="
                                !canNavigateTo(idx)
                                    ? 'Complete previous steps first'
                                    : ''
                            "
                            @click="goToStep(idx)"
                        >
                            <span
                                class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold"
                                :class="
                                    (() => {
                                        const visited = idx < currentStep;
                                        const current = idx === currentStep;
                                        const valid = stepValidityList[idx];
                                        if (visited && valid)
                                            return 'bg-emerald-600 text-white';
                                        if (visited && !valid)
                                            return 'bg-red-600 text-white';
                                        if (current && !isCurrentStepValid)
                                            return 'bg-red-500 text-white';
                                        if (current)
                                            return 'bg-blue-500 text-white';
                                        return 'bg-white/10 text-white/70';
                                    })()
                                "
                            >
                                <template
                                    v-if="
                                        idx < currentStep &&
                                        stepValidityList[idx]
                                    "
                                    >✓</template
                                >
                                <template
                                    v-else-if="
                                        (idx < currentStep ||
                                            idx === currentStep) &&
                                        !stepValidityList[idx]
                                    "
                                    >!</template
                                >
                                <template v-else>{{ idx + 1 }}</template>
                            </span>
                            <span class="truncate">{{ s.title }}</span>
                        </button>
                    </div>
                </div>

                <!-- Wizard Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Step 0: Location -->
                    <div
                        v-if="currentStep === 0"
                        class="border border-white/20 rounded-lg p-4 bg-white/5 backdrop-blur-sm shadow-glow space-y-4"
                    >
                        <div class="flex items-center justify-between">
                            <p class="text-xs sm:text-sm text-white/70">
                                Tip: Click anywhere on the map to set a manual
                                location.
                            </p>
                            <button
                                type="button"
                                @click="useCurrentLocation"
                                :disabled="locating"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-md border border-white/20 bg-white/10 text-white hover:bg-white/15 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <svg
                                    v-if="!locating"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4l3 3"
                                    />
                                </svg>
                                <svg
                                    v-else
                                    class="h-4 w-4 animate-spin"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                        fill="none"
                                        class="opacity-25"
                                    />
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                                    />
                                </svg>
                                <span>{{
                                    locating ? "Locating…" : "Use my location"
                                }}</span>
                            </button>
                        </div>

                        <div
                            class="h-72 rounded overflow-hidden border border-white/20 shadow-glow"
                        >
                            <l-map
                                :zoom="14"
                                :center="[form.lat, form.lng]"
                                :useGlobalLeaflet="true"
                                :max-bounds="bounds"
                                style="height: 18rem"
                                @click="onMapClick"
                            >
                                <l-tile-layer
                                    url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                                />
                                <l-marker :lat-lng="[form.lat, form.lng]" />
                                <l-geo-json
                                    :geojson="surigaoGeoJson"
                                    :options-style="geoJsonStyle"
                                />
                            </l-map>
                        </div>

                        <div
                            class="space-y-2 text-sm bg-white/10 backdrop-blur-sm p-4 rounded shadow-glow border border-white/20"
                        >
                            <div>
                                <span class="font-medium text-white/90"
                                    >Coordinates:</span
                                >
                                <span
                                    class="text-white/70"
                                    :title="`Lat ${form.lat}, Lng ${form.lng}`"
                                    >Lat {{ formatShort(form.lat) }}, Lng
                                    {{ formatShort(form.lng) }}</span
                                >
                            </div>
                            <div>
                                <span class="font-medium text-white/90"
                                    >Location:</span
                                >
                                <span class="text-white/70">{{
                                    form.location_name
                                }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-white/90"
                                    >Map Link:</span
                                >
                                <a
                                    :href="`https://www.google.com/maps?q=${form.lat},${form.lng}`"
                                    target="_blank"
                                    class="text-blue-400 hover:text-blue-300 underline flex items-center transition-colors"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 mr-1"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 20l-5.447-2.724A2 2 0 013 15.382V5a2 2 0 012-2h14a2 2 0 012 2v10.382a2 2 0 01-1.553 1.894L15 20l-3-1.5L9 20z"
                                        />
                                    </svg>
                                    <span>View on Google Maps</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Step 1: Vehicle Type -->
                    <div v-if="currentStep === 1">
                        <label class="block text-sm font-medium text-white mb-2"
                            >Vehicle Type *</label
                        >
                        <div class="grid grid-cols-2 gap-4">
                            <button
                                type="button"
                                @click="form.vehicle_type = 'car'"
                                :class="[
                                    'p-4 border-2 rounded-lg text-center font-medium transition-all backdrop-blur-sm',
                                    form.vehicle_type === 'car'
                                        ? 'border-blue-400 bg-blue-400/20 text-blue-300'
                                        : 'border-white/30 hover:border-white/50 text-white/80 bg-white/10',
                                ]"
                            >
                                🚗 Car
                            </button>
                            <button
                                type="button"
                                @click="form.vehicle_type = 'motorcycle'"
                                :class="[
                                    'p-4 border-2 rounded-lg text-center font-medium transition-all backdrop-blur-sm',
                                    form.vehicle_type === 'motorcycle'
                                        ? 'border-blue-400 bg-blue-400/20 text-blue-300'
                                        : 'border-white/30 hover:border-white/50 text-white/80 bg-white/10',
                                ]"
                            >
                                🏍️ Motorcycle
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Details (guided into 4 mini-steps) -->
                    <div v-if="currentStep === 2" class="space-y-5">
                        <div
                            class="flex items-start justify-between gap-3 flex-wrap"
                        >
                            <h3 class="text-lg font-semibold text-white">
                                Vehicle Details
                            </h3>
                            <div class="text-xs text-white/70">
                                Part {{ detailsSubStep + 1 }} of
                                {{ detailsSubSteps.length }}
                            </div>
                        </div>
                        <div
                            class="w-full bg-white/10 rounded-full h-2 overflow-hidden"
                        >
                            <div
                                class="h-2 bg-emerald-400 transition-all"
                                :style="{
                                    width:
                                        ((detailsSubStep + 1) /
                                            detailsSubSteps.length) *
                                            100 +
                                        '%',
                                }"
                            ></div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                            <div
                                v-for="(label, i) in detailsSubSteps"
                                :key="label"
                                class="text-xs px-2 py-1 rounded border"
                                :class="[
                                    i === detailsSubStep
                                        ? 'bg-white/15 border-white/30 text-white'
                                        : 'bg-white/5 border-white/10 text-white/70',
                                    i < detailsSubStep
                                        ? 'ring-1 ring-emerald-400/50'
                                        : '',
                                ]"
                            >
                                {{ i + 1 }}. {{ label }}
                            </div>
                        </div>

                        <!-- Sub-step 1: Make & Model -->
                        <div
                            v-if="detailsSubStep === 0"
                            class="grid grid-cols-1 md:grid-cols-2 gap-4"
                        >
                            <div>
                                <label
                                    class="block text-sm font-medium text-white mb-1"
                                    >Make *</label
                                >
                                <select
                                    v-model="form.make_id"
                                    @change="onMakeChange"
                                    class="w-full px-3 py-2 rounded border border-white/20 bg-white/10 text-white"
                                >
                                    <option value="" class="bg-gray-800">
                                        Select Make
                                    </option>
                                    <option
                                        v-for="m in filteredMakes"
                                        :key="m.id"
                                        :value="m.id"
                                        class="bg-gray-800"
                                    >
                                        {{ m.name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-white mb-1"
                                    >Model *</label
                                >
                                <select
                                    v-model="form.model_id"
                                    :disabled="!form.make_id || loadingModels"
                                    class="w-full px-3 py-2 rounded border border-white/20 bg-white/10 text-white disabled:opacity-60"
                                >
                                    <option value="" class="bg-gray-800">
                                        Select Model
                                    </option>
                                    <option
                                        v-for="mdl in models"
                                        :key="mdl.id"
                                        :value="mdl.id"
                                        class="bg-gray-800"
                                    >
                                        {{ mdl.name }}
                                    </option>
                                </select>
                                <p
                                    v-if="loadingModels"
                                    class="text-xs text-white/60 mt-1"
                                >
                                    Loading models…
                                </p>
                            </div>
                        </div>

                        <!-- Sub-step 2: Core Specs -->
<div
  v-if="detailsSubStep === 1"
  class="grid grid-cols-1 md:grid-cols-2 gap-6"
>
  <!-- Vehicle Sub-Type -->
  <div>
    <label class="block text-sm font-medium text-white mb-1">
      Vehicle Sub-Type *
    </label>
    <select
      v-model="form.type_id"
      class="w-full px-3 py-2 rounded border border-white/20 bg-white/10 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent backdrop-blur-sm"
      required
    >
      <option value="" class="bg-gray-800 text-white">Select Sub-Type</option>
      <option
        v-for="type in filteredTypes"
        :key="type.id"
        :value="type.id"
        class="bg-gray-800 text-white"
      >
        {{ type.sub_type || type.name }}
      </option>
    </select>
  </div>

  <!-- Year -->
  <div>
    <label class="block text-sm font-medium text-white mb-1">Year *</label>
    <select
      v-model="form.year"
      class="w-full px-3 py-2 rounded border border-white/20 bg-white/10 text-white"
    >
      <option value="" class="bg-gray-800">Select Year</option>
      <option v-for="y in years" :key="y" :value="y" class="bg-gray-800">
        {{ y }}
      </option>
    </select>
  </div>

  <!-- Transmission -->
  <div>
    <label class="block text-sm font-medium text-white mb-1">
      Transmission *
    </label>
    <select
      v-model="form.transmission_id"
      class="w-full px-3 py-2 rounded border border-white/20 bg-white/10 text-white"
    >
      <option value="" class="bg-gray-800">Select Transmission</option>
      <option
        v-for="t in props.transmissions || []"
        :key="t.id"
        :value="t.id"
        class="bg-gray-800"
      >
        {{ t.name }}
      </option>
    </select>
  </div>

  <!-- Fuel Type -->
  <div>
    <label class="block text-sm font-medium text-white mb-1">Fuel Type *</label>
    <select
      v-model="form.fuel_type_id"
      class="w-full px-3 py-2 rounded border border-white/20 bg-white/10 text-white"
    >
      <option value="" class="bg-gray-800">Select Fuel</option>
      <option
        v-for="f in props.fuelTypes || []"
        :key="f.id"
        :value="f.id"
        class="bg-gray-800"
      >
        {{ f.name }}
      </option>
    </select>
  </div>

  <!-- Engine Size -->
  <div>
    <label class="block text-sm font-medium text-white mb-1">
      Engine Size (CC)
    </label>
    <div class="relative">
      <input
        v-model="form.engine_size"
        type="text"
        inputmode="numeric"
        pattern="\\d*"
        placeholder="e.g. 1500"
        @input="(e) => { const v = (e.target.value || '').replace(/\D/g, ''); form.engine_size = v; }"
        class="w-full pr-12 px-3 py-2 rounded border border-white/20 bg-white/10 text-white placeholder-white/40"
      />
      <span
        class="absolute inset-y-0 right-3 flex items-center text-white/60 pointer-events-none"
      >
        CC
      </span>
    </div>
  </div>

  <!-- Horsepower -->
  <div>
    <label class="block text-sm font-medium text-white mb-1">Horsepower</label>
    <input
      v-model="form.horsepower"
      type="number"
      min="0"
            max="2000"
      step="1"
      inputmode="numeric"
            @input="(e) => { const n = parseInt(e.target.value || ''); if (!isNaN(n)) form.horsepower = Math.min(Math.max(n, 0), 2000); }"
      class="no-spinner w-full px-3 py-2 rounded border border-white/20 bg-white/10 text-white"
    />
  </div>
</div>

                        <!-- Sub-step 3: Attributes (now includes Description & Availability) -->
                        <div
                            v-if="detailsSubStep === 2"
                            class="grid grid-cols-1 md:grid-cols-2 gap-4"
                        >
                            <div>
                                <label
                                    class="block text-sm font-medium text-white mb-1"
                                    >License Plate</label
                                >
                                <input
                                    v-model="form.license_plate"
                                    type="text"
                                    placeholder="e.g. ABC-1234"
                                    class="w-full px-3 py-2 rounded border border-white/20 bg-white/10 text-white placeholder-white/40"
                                />
                            </div>
                            <div class="md:col-span-1">
                                <label
                                    class="block text-sm font-medium text-white mb-1"
                                    >Color *</label
                                >
                                <input
                                    v-model="form.color"
                                    type="text"
                                    placeholder="e.g. Red"
                                    @input="onColorInput"
                                    class="w-full px-3 py-2 rounded border border-white/20 bg-white/10 text-white placeholder-white/40"
                                />
                            </div>

                            <template v-if="form.vehicle_type === 'car'">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-white mb-1"
                                        >Doors</label
                                    >
                                    <select
                                        v-model="form.doors"
                                        class="w-full px-3 py-2 rounded border border-white/20 bg-white/10 text-white"
                                    >
                                        <option value="" class="bg-gray-800">Select Doors</option>
                                        <option class="bg-gray-800" value="2">2</option>
                                        <option class="bg-gray-800" value="3">3</option>
                                        <option class="bg-gray-800" value="4">4</option>
                                        <option class="bg-gray-800" value="5">5</option>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-white mb-1"
                                        >Seats</label
                                    >
                                    <select
                                        v-model="form.seats"
                                        class="w-full px-3 py-2 rounded border border-white/20 bg-white/10 text-white"
                                    >
                                        <option value="" class="bg-gray-800">Select Seats</option>
                                        <option class="bg-gray-800" value="2">2</option>
                                        <option class="bg-gray-800" value="4">4</option>
                                        <option class="bg-gray-800" value="5">5</option>
                                        <option class="bg-gray-800" value="7">7</option>
                                        <option class="bg-gray-800" value="8">8</option>
                                    </select>
                                </div>
                            </template>
                            <template v-else>
                                <div class="md:col-span-2">
                                    <label
                                        class="block text-sm font-medium text-white mb-1"
                                        >Seats</label
                                    >
                                    <select
                                        v-model="form.seats"
                                        class="w-full px-3 py-2 rounded border border-white/20 bg-white/10 text-white"
                                    >
                                        <option value="" class="bg-gray-800">Select Seats</option>
                                        <option class="bg-gray-800" value="1">1</option>
                                        <option class="bg-gray-800" value="2">2</option>
                                    </select>
                                </div>
                            </template>
                            <div class="md:col-span-2">
                                <label
                                    class="block text-sm font-medium text-white mb-1"
                                    >Description</label
                                >
                                <textarea
                                    v-model="form.description"
                                    rows="4"
                                    placeholder="Add any notes, condition, or special instructions"
                                    class="w-full px-3 py-2 rounded border border-white/20 bg-white/10 text-white placeholder-white/40"
                                ></textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="inline-flex items-center gap-2 text-white/90">
                                    <input
                                        type="checkbox"
                                        v-model="form.is_available"
                                        class="rounded border-white/20 bg-white/10"
                                    />
                                    <span>Available for booking</span>
                                </label>
                            </div>
                        </div>

                        <p class="text-xs text-white/60">
                            Use the Continue button below to proceed to the next
                            mini-step.
                        </p>
                    </div>

                    <!-- Step 3: Photo -->
                    <div
                        v-show="currentStep === 3"
                        class="main-photo-section border border-white/20 rounded-lg p-6 bg-white/5 backdrop-blur-sm shadow-glow space-y-4"
                    >
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-white">
                                Main Vehicle Photo
                            </h3>
                            <span
                                class="text-xs text-white/60 bg-white/10 px-2 py-1 rounded-full"
                                >Required</span
                            >
                        </div>
                        <div class="photo-uploader-wrapper">
                            <CustomImageUploader
                                :multiple="false"
                                @file-selected="onPhotoChange"
                            />
                        </div>
                        <div
                            class="photo-guidelines bg-white/5 border border-white/10 rounded-lg p-4"
                        >
                            <h4 class="text-sm font-medium text-white mb-2">
                                📸 Photo Guidelines
                            </h4>
                            <ul class="text-xs text-white/70 space-y-1">
                                <li>• Use high-quality, well-lit photos</li>
                                <li>• Show the vehicle's exterior clearly</li>
                                <li>• Avoid blurry or dark images</li>
                                <li>• Recommended size: 1200x800px</li>
                                <li>• Supported formats: JPEG, PNG, WebP</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Step 4: Pricing -->
                    <div v-if="currentStep === 4" class="border-t pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg text-white font-semibold">
                                Pricing
                            </h3>
                            <button
                                type="button"
                                @click="loadPricingTiers"
                                :disabled="loadingPricingTiers"
                                class="text-white flex items-center px-3 py-1 text-sm text-blue-600 hover:text-blue-800 border border-blue-300 hover:border-blue-400 rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <svg
                                    :class="[
                                        'w-4 h-4 mr-1',
                                        { 'animate-spin': loadingPricingTiers },
                                    ]"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke-width="4"
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                                    ></path>
                                </svg>
                                {{
                                    loadingPricingTiers
                                        ? "Refreshing..."
                                        : "Refresh"
                                }}
                            </button>
                        </div>
                        <div v-if="pricingTiers.length > 0">
                            <p class="text-sm text-gray-600 mb-3 text-white">
                                Select pricing tiers for this vehicle:
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <label
                                    v-for="tier in pricingTiers"
                                    :key="tier.id"
                                    class="flex text-white items-center p-3 border rounded-md cursor-pointer hover:bg-gray-500"
                                >
                                    <input
                                        type="checkbox"
                                        :value="tier.id"
                                        v-model="selectedTierIds"
                                        class="mr-3 h-4 w-4 text-blue-600"
                                    />
                                    <div>
                                        <div class="font-medium text-white">
                                            {{ tier.duration_from }}
                                            {{ tier.duration_unit }}
                                        </div>
                                        <div class="text-sm white">
                                            ₱{{ tier.price }}
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div
                            v-else-if="loadingPricingTiers"
                            class="text-sm text-gray-500"
                        >
                            Loading pricing tiers...
                        </div>
                        <div v-else class="text-sm text-gray-500">
                            No pricing tiers available.
                        </div>
                        <p class="text-sm text-gray-300 mt-2">
                            You can manage pricing tiers in the
                            <Link
                                href="/owner/pricing-tiers"
                                class="text-blue-600 underline"
                                >pricing management</Link
                            >
                            section.
                        </p>
                    </div>

                    <!-- Step 5: Review -->
                    <div
                        v-if="currentStep === 5"
                        class="border border-white/20 rounded-lg p-4 bg-white/5 backdrop-blur-sm shadow-glow space-y-4"
                    >
                        <h3 class="text-lg font-semibold text-white">
                            Review & Confirm
                        </h3>
                        <p class="text-white/70 text-sm">
                            Please review your details before creating the
                            vehicle. You can click Edit on any section to make
                            changes.
                        </p>

                        <div class="space-y-3">
                            <div
                                class="bg-white/5 border border-white/10 rounded p-3"
                            >
                                <div class="flex items-center justify-between">
                                    <h4 class="text-white font-medium">
                                        Location
                                    </h4>
                                    <button
                                        type="button"
                                        class="text-blue-300 hover:text-blue-200 text-sm"
                                        @click="goToStep(0)"
                                    >
                                        Edit
                                    </button>
                                </div>
                                <div class="text-white/80 text-sm mt-2">
                                    <div>
                                        <span class="text-white/60">Name:</span>
                                        {{ form.location_name || "—" }}
                                    </div>
                                    <div>
                                        <span class="text-white/60"
                                            >Coords:</span
                                        >
                                        <span
                                            :title="`Lat ${form.lat}, Lng ${form.lng}`"
                                            >{{ formatShort(form.lat) }},
                                            {{ formatShort(form.lng) }}</span
                                        >
                                    </div>
                                </div>
                            </div>

                            <div
                                class="bg-white/5 border border-white/10 rounded p-3"
                            >
                                <div class="flex items-center justify-between">
                                    <h4 class="text-white font-medium">
                                        Vehicle Type
                                    </h4>
                                    <button
                                        type="button"
                                        class="text-blue-300 hover:text-blue-200 text-sm"
                                        @click="goToStep(1)"
                                    >
                                        Edit
                                    </button>
                                </div>
                                <div class="text-white/80 text-sm mt-2">
                                    {{
                                        form.vehicle_type === "car"
                                            ? "Car"
                                            : form.vehicle_type === "motorcycle"
                                            ? "Motorcycle"
                                            : "—"
                                    }}
                                </div>
                            </div>

                            <div
                                class="bg-white/5 border border-white/10 rounded p-3"
                            >
                                <div class="flex items-center justify-between">
                                    <h4 class="text-white font-medium">
                                        Details
                                    </h4>
                                    <button
                                        type="button"
                                        class="text-blue-300 hover:text-blue-200 text-sm"
                                        @click="goToStep(2)"
                                    >
                                        Edit
                                    </button>
                                </div>
                                <div
                                    class="text-white/80 text-sm mt-2 grid grid-cols-1 sm:grid-cols-2 gap-y-1"
                                >
                                    <div>
                                        <span class="text-white/60">Make:</span>
                                        {{ makeName }}
                                    </div>
                                    <div>
                                        <span class="text-white/60"
                                            >Model:</span
                                        >
                                        {{ modelName }}
                                    </div>
                                    <div>
                                        <span class="text-white/60">Year:</span>
                                        {{ form.year || "—" }}
                                    </div>
                                    <div>
                                        <span class="text-white/60"
                                            >Transmission:</span
                                        >
                                        {{ transmissionName }}
                                    </div>
                                    <div>
                                        <span class="text-white/60">Fuel:</span>
                                        {{ fuelTypeName }}
                                    </div>
                                    <div>
                                        <span class="text-white/60"
                                            >Color:</span
                                        >
                                        {{ form.color || "—" }}
                                    </div>
                                    <div v-if="typeName">
                                        <span class="text-white/60"
                                            >Sub-Type:</span
                                        >
                                        {{ typeName }}
                                    </div>
                                    <div class="sm:col-span-2">
                                        <span class="text-white/60"
                                            >License Plate:</span
                                        >
                                        {{ form.license_plate || "—" }}
                                    </div>
                                    <div class="sm:col-span-2 mt-2">
                                        <span class="text-white/60"
                                            >Description:</span
                                        >
                                        {{ form.description || "—" }}
                                    </div>
                                    <div>
                                        <span class="text-white/60">Availability:</span>
                                        {{ form.is_available ? 'Available' : 'Not available' }}
                                    </div>
                                    <div>
                                        <span class="text-white/60"
                                            >Engine Size:</span
                                        >
                                        {{ form.engine_size ? form.engine_size + ' CC' : '—' }}
                                    </div>
                                    <div>
                                        <span class="text-white/60"
                                            >Horsepower:</span
                                        >
                                        {{ form.horsepower || "—" }}
                                    </div>
                                    <div v-if="form.vehicle_type === 'car'">
                                        <span class="text-white/60"
                                            >Doors:</span
                                        >
                                        {{ form.doors || "—" }}
                                    </div>
                                    <div v-if="form.vehicle_type === 'car'">
                                        <span class="text-white/60"
                                            >Seats:</span
                                        >
                                        {{ form.seats || "—" }}
                                    </div>
                                    <div v-else-if="form.vehicle_type === 'motorcycle'">
                                        <span class="text-white/60">Seats:</span>
                                        {{ form.seats || "—" }}
                                    </div>
                                </div>
                            </div>

                            <div
                                class="bg-white/5 border border-white/10 rounded p-3"
                            >
                                <div class="flex items-center justify-between">
                                    <h4 class="text-white font-medium">
                                        Photo
                                    </h4>
                                    <button
                                        type="button"
                                        class="text-blue-300 hover:text-blue-200 text-sm"
                                        @click="goToStep(3)"
                                    >
                                        Edit
                                    </button>
                                </div>
                                <div class="text-white/80 text-sm mt-2">
                                    <span v-if="photoFile"
                                        >New photo selected</span
                                    >
                                    <span v-else>No photo selected yet</span>
                                </div>
                            </div>

                            <div
                                class="bg-white/5 border border-white/10 rounded p-3"
                            >
                                <div class="flex items-center justify-between">
                                    <h4 class="text-white font-medium">
                                        Pricing Tiers
                                    </h4>
                                    <button
                                        type="button"
                                        class="text-blue-300 hover:text-blue-200 text-sm"
                                        @click="goToStep(4)"
                                    >
                                        Edit
                                    </button>
                                </div>
                                <div class="text-white/80 text-sm mt-2">
                                    <span v-if="selectedTierIds.length"
                                        >{{
                                            selectedTierIds.length
                                        }}
                                        selected</span
                                    >
                                    <span v-else>None selected</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div
                        class="border-t border-white/20 pt-6 flex items-center justify-between gap-3"
                    >
                        <button
                            type="button"
                            class="px-4 py-2 rounded-md border border-white/20 bg-white/10 text-white hover:bg-white/15 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="currentStep === 0 || submitting"
                            @click="goPrev"
                        >
                            Back
                        </button>
                        <div class="ml-auto flex items-center gap-2">
                            <button
                                v-if="currentStep < steps.length - 1"
                                type="button"
                                :disabled="!isCurrentStepValid || submitting"
                                class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 disabled:bg-gray-500 disabled:opacity-50 text-white shadow"
                                @click="goNext"
                            >
                                Continue
                            </button>
                            <button
                                v-else
                                type="submit"
                                :disabled="submitting || !isCurrentStepValid"
                                class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 disabled:bg-gray-500 disabled:opacity-50 text-white shadow"
                            >
                                {{
                                    submitting
                                        ? "Creating Vehicle..."
                                        : "Create Vehicle"
                                }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </OwnerLayout>

    <!-- Out of Range / Geolocation Modal -->
    <div
        v-if="showOutOfRangeModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
        @click.self="showOutOfRangeModal = false"
    >
        <div
            class="glass-card-dark max-w-md w-full rounded-lg border border-white/20 shadow-glow"
        >
            <div
                class="p-5 border-b border-white/10 flex items-center justify-between"
            >
                <h3 class="text-white font-semibold">Location Notice</h3>
                <button
                    class="text-white/70 hover:text-white"
                    @click="showOutOfRangeModal = false"
                >
                    ✕
                </button>
            </div>
            <div class="p-5 text-white/90 space-y-3">
                <p>{{ outOfRangeMessage }}</p>
                <ul
                    class="list-disc list-inside text-white/70 text-sm space-y-1"
                >
                    <li>
                        Ensure location services are enabled and grant
                        permission.
                    </li>
                    <li>
                        If you are outside Surigao del Norte city limits, pick a
                        valid in-city meeting point manually.
                    </li>
                </ul>
            </div>
            <div class="p-5 pt-0 flex justify-end gap-2">
                <button
                    class="px-4 py-2 rounded-md border border-white/20 text-white/90 hover:bg-white/10"
                    @click="showOutOfRangeModal = false"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from "vue";
import { router, Link } from "@inertiajs/vue3";
import { throttle } from "lodash-es";

import OwnerLayout from "@/Layouts/OwnerLayout.vue";
import { LMap, LTileLayer, LMarker, LGeoJson } from "@vue-leaflet/vue-leaflet";
import CustomImageUploader from "@/Components/CustomImageUploader.vue";

const props = defineProps({
    fuelTypes: Array,
    transmissions: Array,
    types: Array,
});

// Wizard state
const steps = [
    { key: "location", title: "Location" },
    { key: "type", title: "Type" },
    { key: "details", title: "Details" },
    { key: "photo", title: "Photo" },
    { key: "pricing", title: "Pricing" },
    { key: "review", title: "Review" },
];
const currentStep = ref(0);
// Details sub-steps to reduce cognitive load (Notes merged into Attributes)
const detailsSubSteps = ["Make & Model", "Core Specs", "Attributes"];
const detailsSubStep = ref(0);

// Input helpers
function onColorInput(e) {
    const v = (e?.target?.value || "").trim();
    if (!v) {
        form.color = "";
        return;
    }
    // Make first letter uppercase and rest lowercase for each word
    form.color = v
        .split(/\s+/)
        .map((w) => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase())
        .join(" ");
}

const form = reactive({
    vehicle_type: "",
    make_id: "",
    model_id: "",
    type_id: "",
    fuel_type_id: "",
    transmission_id: "",
    year: "",
    license_plate: "",
    color: "",
    engine_size: "",
    horsepower: "",
    doors: "", // cars only
    seats: "", // cars only
    description: "",
    is_available: true,
    lat: 9.785903415098108, // Default to Surigao del Norte
    lng: 125.49062330098809,
    location_name: "Surigao del Norte, Philippines",
});

const makes = ref([]);
const models = ref([]);
const pricingTiers = ref([]);
const selectedTierIds = ref([]);
const loadingModels = ref(false);
const submitting = ref(false);
const loadingPricingTiers = ref(false);
const photoFile = ref(null);
const photoPreview = ref(null);
const locating = ref(false);
const showOutOfRangeModal = ref(false);
const outOfRangeMessage = ref(
    "Your current location appears to be outside the supported service area."
);

const surigaoGeoJson = {
    type: "FeatureCollection",
    features: [
        {
            type: "Feature",
            properties: {},
            geometry: {
                type: "Polygon",
                coordinates: [
                    [
                        [125.48545720374489, 9.801207813887503],
                        [125.48394937158277, 9.801134816678099],
                        [125.4824560626428, 9.800916528148765],
                        [125.48099166020462, 9.800555050821249],
                        [125.47957026901334, 9.800053866380772],
                        [125.47820557937605, 9.799417802127778],
                        [125.47691073525756, 9.79865298446448],
                        [125.4756982076485, 9.797766779865192],
                        [125.47457967442702, 9.796767723899952],
                        [125.47356590787237, 9.79566543899633],
                        [125.47266667091462, 9.79447054173279],
                        [125.47189062311993, 9.793194540557817],
                        [125.47124523731648, 9.791849724921354],
                        [125.47073672766314, 9.790449046887577],
                        [125.47036998985222, 9.789005996370452],
                        [125.47014855402033, 9.787534471194746],
                        [125.47007455081837, 9.786048643234757],
                        [125.47014869096546, 9.784562821920598],
                        [125.47037025847982, 9.78309131642699],
                        [125.47073711764999, 9.781648297871861],
                        [125.47124573367556, 9.78024766285169],
                        [125.47189120677649, 9.77890289962746],
                        [125.47266731943903, 9.77762695824917],
                        [125.47356659634224, 9.776432125868816],
                        [125.47458037638481, 9.775329908441458],
                        [125.47569889611839, 9.774330919952476],
                        [125.47691138378202, 9.773444780236375],
                        [125.47820616303265, 9.772680022369768],
                        [125.47957076537246, 9.772044010529008],
                        [125.48099205019147, 9.771542869102143],
                        [125.48245633127043, 9.771181423736623],
                        [125.48394950852794, 9.77096315488939],
                        [125.48545720374489, 9.770890164325817],
                        [125.48696489896184, 9.77096315488939],
                        [125.48845807621933, 9.771181423736623],
                        [125.48992235729831, 9.771542869102143],
                        [125.49134364211733, 9.772044010529008],
                        [125.49270824445713, 9.772680022369768],
                        [125.49400302370776, 9.773444780236375],
                        [125.4952155113714, 9.774330919952476],
                        [125.49633403110496, 9.775329908441458],
                        [125.49734781114755, 9.776432125868816],
                        [125.49824708805076, 9.77762695824917],
                        [125.49902320071328, 9.77890289962746],
                        [125.4996686738142, 9.78024766285169],
                        [125.5001772898398, 9.781648297871861],
                        [125.50054414900994, 9.78309131642699],
                        [125.50076571652433, 9.784562821920598],
                        [125.50083985667139, 9.786048643234757],
                        [125.50076585346946, 9.787534471194746],
                        [125.50054441763756, 9.789005996370452],
                        [125.50017767982663, 9.790449046887577],
                        [125.4996691701733, 9.791849724921354],
                        [125.49902378436985, 9.793194540557817],
                        [125.49824773657517, 9.79447054173279],
                        [125.4973484996174, 9.79566543899633],
                        [125.49633473306275, 9.796767723899952],
                        [125.49521619984127, 9.797766779865192],
                        [125.49400367223222, 9.79865298446448],
                        [125.49270882811372, 9.799417802127778],
                        [125.49134413847645, 9.800053866380772],
                        [125.48992274728516, 9.800555050821249],
                        [125.48845834484696, 9.800916528148765],
                        [125.48696503590699, 9.801134816678099],
                        [125.48545720374489, 9.801207813887503],
                    ],
                ],
            },
        },
    ],
};

const getLocationName = throttle(async () => {
    if (form.lat && form.lng) {
        try {
            const res = await fetch(
                `https://nominatim.openstreetmap.org/reverse?lat=${form.lat}&lon=${form.lng}&format=json`
            );
            const data = await res.json();
            form.location_name = data.display_name;
        } catch (err) {
            locationName.value = "Unknown location";
            form.location_name = "";
        }
    }
}, 1000);

const geoJsonStyle = () => ({
    color: "#1976d2",
    weight: 2,
    fill: false,
});

const bounds = [
    [9.770890164325817, 125.47007455081837], // Southwest
    [9.801207813887503, 125.50083985667139], // Northeast
];
watch(
    () => [form.lat, form.lng],
    () => {
        getLocationName();
    },
    { immediate: true }
);

// Generate years from 2000 to current year + 1
const years = computed(() => {
    const currentYear = new Date().getFullYear();
    const yearList = [];
    for (let year = currentYear + 1; year >= 2000; year--) {
        yearList.push(year);
    }
    return yearList;
});

const filteredMakes = computed(() => {
    if (!form.vehicle_type) return [];
    return makes.value.filter(
        (make) => make.vehicle_type === form.vehicle_type
    );
});

const filteredTypes = computed(() => {
    if (!form.vehicle_type) return props.types || [];
    // Map vehicle_type to match VehicleType category
    const categoryMapping = {
        car: "car",
        motorcycle: "motorcycle",
    };
    const targetCategory = categoryMapping[form.vehicle_type];
    return (props.types || []).filter(
        (type) => type.category === targetCategory
    );
});

// Load makes when component mounts
onMounted(async () => {
    await loadMakes();
    await loadPricingTiers();
});

// Watch for vehicle type changes
watch(
    () => form.vehicle_type,
    () => {
        form.make_id = "";
        form.model_id = "";
        form.type_id = "";
        form.engine_size = "";
        form.horsepower = "";
        form.doors = "";
        form.seats = "";
        models.value = [];
        detailsSubStep.value = 0;
    }
);

async function loadMakes() {
    try {
        const response = await fetch("/api/vehicle-data/makes/car");
        if (response.ok) {
            const carMakes = await response.json();
            const motorcycleResponse = await fetch(
                "/api/vehicle-data/makes/motorcycle"
            );
            if (motorcycleResponse.ok) {
                const motorcycleMakes = await motorcycleResponse.json();
                makes.value = [...carMakes, ...motorcycleMakes];
            }
        }
    } catch (error) {
        console.error("Error loading makes:", error);
    }
}

async function loadPricingTiers() {
    loadingPricingTiers.value = true;
    try {
        const response = await fetch("/owner/pricing-tiers/list");
        if (response.ok) {
            const data = await response.json();
            pricingTiers.value = data.pricingTiers || [];
        }
    } catch (error) {
        console.error("Error loading pricing tiers:", error);
    } finally {
        loadingPricingTiers.value = false;
    }
}

async function onMakeChange() {
    if (!form.make_id) {
        models.value = [];
        return;
    }

    loadingModels.value = true;
    form.model_id = "";

    try {
        const response = await fetch(
            `/api/vehicle-data/models/${form.make_id}`
        );
        if (response.ok) {
            models.value = await response.json();
        } else {
            models.value = [];
        }
    } catch (error) {
        console.error("Error loading models:", error);
        models.value = [];
    } finally {
        loadingModels.value = false;
    }
}

function onPhotoChange(file) {
    photoFile.value = file;
    // FilePond handles preview automatically, no need for manual preview
}

// Map-related functions

function onMapClick(event) {
    form.lat = event.latlng.lat;
    form.lng = event.latlng.lng;

    // Reverse geocoding to get location name
    reverseGeocode(form.lat, form.lng);
}

// Determine if a point (lat, lng) is within the Surigao polygon
function isWithinCity(lat, lng) {
    try {
        const poly = surigaoGeoJson.features?.[0]?.geometry?.coordinates?.[0];
        if (!poly || !Array.isArray(poly)) return false;

        const x = lng; // polygon is [lng, lat]
        const y = lat;
        let inside = false;
        for (let i = 0, j = poly.length - 1; i < poly.length; j = i++) {
            const xi = poly[i][0],
                yi = poly[i][1];
            const xj = poly[j][0],
                yj = poly[j][1];
            const intersect =
                yi > y !== yj > y &&
                x < ((xj - xi) * (y - yi)) / (yj - yi) + xi;
            if (intersect) inside = !inside;
        }
        return inside;
    } catch (e) {
        return false;
    }
}

function useCurrentLocation() {
    if (!("geolocation" in navigator)) {
        outOfRangeMessage.value =
            "Geolocation is not supported by your browser. Please set the location manually on the map.";
        showOutOfRangeModal.value = true;
        return;
    }
    locating.value = true;
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            locating.value = false;
            const { latitude, longitude } = pos.coords;
            if (isWithinCity(latitude, longitude)) {
                form.lat = latitude;
                form.lng = longitude;
                reverseGeocode(latitude, longitude);
            } else {
                outOfRangeMessage.value =
                    "Your current location is outside Surigao del Norte city bounds. Please pick a valid location within the highlighted area.";
                showOutOfRangeModal.value = true;
            }
        },
        (err) => {
            locating.value = false;
            if (err.code === 1) {
                outOfRangeMessage.value =
                    "Permission to access location was denied. You can enable it in your browser settings or set the location manually.";
            } else if (err.code === 2) {
                outOfRangeMessage.value =
                    "Position unavailable. Please try again or set the location manually.";
            } else if (err.code === 3) {
                outOfRangeMessage.value =
                    "Location request timed out. Please try again or set the location manually.";
            } else {
                outOfRangeMessage.value =
                    "Unable to get your current location. Please set the location manually.";
            }
            showOutOfRangeModal.value = true;
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}

async function reverseGeocode(lat, lng) {
    try {
        const response = await fetch(
            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`
        );

        if (response.ok) {
            const data = await response.json();
            form.location_name = data.display_name
                .split(",")
                .slice(0, 3)
                .join(", ");
        }
    } catch (error) {
        console.error("Reverse geocoding error:", error);
        form.location_name = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    }
}

// Lookups for review step
const makeName = computed(() => {
    const m = makes.value.find((m) => String(m.id) === String(form.make_id));
    return m?.name || "";
});
const modelName = computed(() => {
    const m = models.value.find((m) => String(m.id) === String(form.model_id));
    return m?.name || "";
});
const typeName = computed(() => {
    const t = (props.types || []).find(
        (t) => String(t.id) === String(form.type_id)
    );
    return t?.name || "";
});
const fuelTypeName = computed(() => {
    const f = (props.fuelTypes || []).find(
        (f) => String(f.id) === String(form.fuel_type_id)
    );
    return f?.name || "";
});
const transmissionName = computed(() => {
    const t = (props.transmissions || []).find(
        (t) => String(t.id) === String(form.transmission_id)
    );
    return t?.name || "";
});

// Short formatter for coordinates (e.g., 9.785903 -> 9.7859)
function formatShort(val, digits = 4) {
    if (val === null || val === undefined || isNaN(val)) return "—";
    const n = Number(val);
    return n.toFixed(digits);
}

// Step validation
const isCurrentStepValid = computed(() => {
    const key = steps[currentStep.value].key;
    if (key === "location") {
        return !!form.lat && !!form.lng && !!form.location_name;
    }
    if (key === "type") {
        return !!form.vehicle_type;
    }
    if (key === "details") {
        // Sub-step specific validation for smoother flow
        if (detailsSubStep.value === 0) {
            return !!form.make_id && !!form.model_id;
        }
        if (detailsSubStep.value === 1) {
            return (
                !!form.type_id &&
                !!form.year &&
                !!form.transmission_id &&
                !!form.fuel_type_id
            );
        }
        if (detailsSubStep.value === 2) {
            return !!form.color;
        }
        // Notes/availability are optional
        return true;
    }
    if (key === "photo") {
        return !!photoFile.value; // required
    }
    if (key === "pricing") {
        return true; // optional
    }
    if (key === "review") {
        // ensure all required across steps
        return (
            !!form.lat &&
            !!form.lng &&
            !!form.vehicle_type &&
            !!form.make_id &&
            !!form.model_id &&
            !!form.year &&
            !!form.transmission_id &&
            !!form.fuel_type_id &&
            !!form.color &&
            !!form.type_id &&
            !!photoFile.value
        );
    }
    return true;
});

// Produce a validity list for all steps to drive the stepper indicators
const stepValidityList = computed(() => {
    return steps.map((s) => {
        switch (s.key) {
            case "location":
                return !!form.lat && !!form.lng && !!form.location_name;
            case "type":
                return !!form.vehicle_type;
            case "details":
                return (
                    !!form.make_id &&
                    !!form.model_id &&
                    !!form.type_id &&
                    !!form.year &&
                    !!form.transmission_id &&
                    !!form.fuel_type_id &&
                    !!form.color
                );
            case "photo":
                return !!photoFile.value;
            case "pricing":
                return true;
            case "review":
                return (
                    !!form.lat &&
                    !!form.lng &&
                    !!form.vehicle_type &&
                    !!form.make_id &&
                    !!form.model_id &&
                    !!form.year &&
                    !!form.transmission_id &&
                    !!form.fuel_type_id &&
                    !!form.color &&
                    !!form.type_id &&
                    !!photoFile.value
                );
            default:
                return true;
        }
    });
});

function goNext() {
    if (!isCurrentStepValid.value) return;
    const key = steps[currentStep.value].key;
    if (key === "details") {
        if (detailsSubStep.value < detailsSubSteps.length - 1) {
            detailsSubStep.value++;
            return;
        }
    }
    if (currentStep.value < steps.length - 1) currentStep.value++;
}
function goPrev() {
    const key = steps[currentStep.value].key;
    if (key === "details" && detailsSubStep.value > 0) {
        detailsSubStep.value--;
        return;
    }
    if (currentStep.value > 0) currentStep.value--;
}
function goToStep(idx) {
    if (!canNavigateTo(idx)) return;
    currentStep.value = idx;
    if (steps[idx].key !== "details") {
        // Reset mini-step when leaving details
        detailsSubStep.value = 0;
    }
}

// Only allow navigating to step i if all previous required steps are valid
function canNavigateTo(idx) {
    if (idx < 0 || idx >= steps.length) return false;
    if (idx <= currentStep.value) return true; // always allow going back
    // moving forward via clicking: all steps < idx must be valid
    for (let i = 0; i < idx; i++) {
        if (!stepValidityList.value[i]) return false;
    }
    return true;
}

function submit() {
    // Guard: if not on final step, advance instead of submitting (handles Enter key too)
    if (currentStep.value < steps.length - 1) {
        if (isCurrentStepValid.value) currentStep.value++;
        return;
    }
    submitting.value = true;

    const formData = new FormData();

    // Add all form fields
    Object.entries(form).forEach(([key, value]) => {
        if (key === "is_available") {
            formData.append(key, value ? "1" : "0");
        } else if (value !== null && value !== "") {
            formData.append(key, value);
        }
    });

    // Add photo if selected
    if (photoFile.value) {
        formData.append("main_photo", photoFile.value);
    }

    // Add pricing tiers
    formData.append("pricing_tier_ids", JSON.stringify(selectedTierIds.value));

    router.post("/owner/vehicles", formData, {
        onFinish: () => {
            submitting.value = false;
        },
        onError: (errors) => {
            console.error("Validation errors:", errors);
        },
    });
}
</script>
<style>
@import "leaflet/dist/leaflet.css";

/* Main photo section styling */
.main-photo-section {
    position: relative;
    overflow: hidden;
}

.photo-uploader-wrapper {
    position: relative;
    z-index: 1;
}

.photo-guidelines {
    margin-top: 1rem;
}

.photo-guidelines h4 {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Animation for photo section */
.main-photo-section {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .main-photo-section {
        padding: 1rem;
    }
}

/* Hide spinners for numeric inputs (horsepower, etc.) */
input[type=number].no-spinner::-webkit-outer-spin-button,
input[type=number].no-spinner::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type=number].no-spinner {
    appearance: textfield;
    -moz-appearance: textfield; /* Firefox */
}
</style>
