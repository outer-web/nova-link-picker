<template>
    <DefaultField
        :field="field"
        :errors="errors"
        :show-help-text="showHelpText"
        :full-width-content="fullWidthContent"
    >
        <template #field>
            <div class="relative flex w-full">
                <select
                    v-model="value.routeName"
                    class="block w-full form-control form-select form-select-bordered"
                >
                    <option value="" selected>
                        {{ __("Select an option") }}
                    </option>
                    <optgroup :label="__('Internal')">
                        <option
                            v-for="route in field.availableRoutes"
                            :value="route.name"
                        >
                            {{ route.label }}
                        </option>
                    </optgroup>
                    <optgroup :label="__('External')">
                        <option
                            v-for="externalRoute in externalRoutes"
                            :value="externalRoute.name"
                        >
                            {{ externalRoute.label }}
                        </option>
                    </optgroup>
                </select>
                <svg
                    class="flex-shrink-0 pointer-events-none form-select-arrow"
                    xmlns="http://www.w3.org/2000/svg"
                    width="10"
                    height="6"
                    viewBox="0 0 10 6"
                >
                    <path
                        class="fill-current"
                        d="M8.292893.292893c.390525-.390524 1.023689-.390524 1.414214 0 .390524.390525.390524 1.023689 0 1.414214l-4 4c-.390525.390524-1.023689.390524-1.414214 0l-4-4c-.390524-.390525-.390524-1.023689 0-1.414214.390525-.390524 1.023689-.390524 1.414214 0L5 3.585786 8.292893.292893z"
                    ></path>
                </svg>
            </div>
            <div v-if="availableParameters.length" class="mt-3">
                <p class="block mb-3 leading-tight">{{ __("Parameters") }}:</p>
                <div
                    class="relative flex items-center w-full mt-3"
                    v-for="parameter in availableParameters"
                    :key="parameter.name"
                >
                    <p class="block mr-3 leading-tight whitespace-nowrap">
                        {{ parameter.label }}
                        <span
                            class="text-sm text-red-500"
                            v-if="parameter.isRequired"
                            >*</span
                        >
                    </p>
                    <input
                        type="text"
                        class="w-full form-control form-input form-input-bordered"
                        :placeholder="parameter.label"
                        v-model="
                            parameters.find(
                                (item) => item.name === parameter.name
                            ).value
                        "
                        v-if="parameter.type === 'string'"
                    />
                    <div
                        class="relative flex w-full"
                        v-if="parameter.type === 'model'"
                    >
                        <select
                            v-model="
                                value.parameters.find(
                                    (item) => item.name === parameter.name
                                ).value
                            "
                            class="block w-full form-control form-select form-select-bordered"
                        >
                            <option value="">
                                {{ __("Select an option") }}
                            </option>
                            <option
                                v-for="model in parameter.options"
                                :value="model.value"
                            >
                                {{ model.label }}
                            </option>
                        </select>
                        <svg
                            class="flex-shrink-0 pointer-events-none form-select-arrow"
                            xmlns="http://www.w3.org/2000/svg"
                            width="10"
                            height="6"
                            viewBox="0 0 10 6"
                        >
                            <path
                                class="fill-current"
                                d="M8.292893.292893c.390525-.390524 1.023689-.390524 1.414214 0 .390524.390525.390524 1.023689 0 1.414214l-4 4c-.390525.390524-1.023689.390524-1.414214 0l-4-4c-.390524-.390525-.390524-1.023689 0-1.414214.390525-.390524 1.023689-.390524 1.414214 0L5 3.585786 8.292893.292893z"
                            ></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div v-if="availableOptions.length" class="mt-3">
                <p class="block mb-3 leading-tight">{{ __("Options") }}:</p>
                <div
                    class="relative flex items-center w-full mt-3"
                    v-for="option in availableOptions"
                    :key="option.name"
                >
                    <checkbox
                        v-if="option.type === 'checkbox'"
                        :id="option.name"
                        @input="
                            value.options.find(
                                (item) => item.name === option.name
                            ).value = $event.target.checked
                        "
                        :checked="
                            value.options.find(
                                (item) => item.name === option.name
                            ).value
                        "
                    />
                    <label
                        :for="option.name"
                        class="ml-2 leading-tight whitespace-nowrap"
                    >
                        {{ option.label }}
                        <span
                            class="text-sm text-red-500"
                            v-if="option.isRequired"
                            >*</span
                        >
                    </label>
                </div>
            </div>
        </template>
    </DefaultField>
</template>

<script>
import { FormField, HandlesValidationErrors, Localization } from "laravel-nova";

export default {
    mixins: [FormField, HandlesValidationErrors, Localization],

    props: ["resourceName", "resourceId", "field"],

    data() {
        return {
            availableParameters: [],
            availableOptions: [],
            externalRoutes: [
                {
                    name: "external.link",
                    label: this.__("Link"),
                },
                {
                    name: "external.mailto",
                    label: this.__("Mailto"),
                },
                {
                    name: "external.tel",
                    label: this.__("Tel"),
                },
            ],
        };
    },

    computed: {
        routeName() {
            return this.value.routeName || "";
        },
        parameters() {
            return this.value.parameters || [];
        },
        options() {
            return this.value.options || [];
        },
    },

    watch: {
        routeName(selectedRouteName) {
            this.availableParameters = [];
            this.availableOptions = [];

            Object.entries(this.field.availableRoutes).forEach(
                ([key, route]) => {
                    if (route.name !== selectedRouteName) {
                        return;
                    }

                    for (let parameter of route.parameters) {
                        // Create a new parameter object.
                        parameter = {
                            name: parameter.name,
                            label: parameter.label,
                            type: parameter.type,
                            model: parameter.model,
                            isRequired: !parameter.isOptional,
                            options: [],
                            value: "",
                        };

                        this.availableParameters.push(parameter);

                        // Add parameter to this.value.parameters if it doesn't exist.
                        if (
                            !this.parameters.find(
                                (item) => item.name === parameter.name
                            )
                        ) {
                            this.parameters.push({
                                name: parameter.name,
                                value: parameter.value,
                            });
                        }

                        // If the parameter is a model, get the options.
                        if (parameter.type === "model") {
                            Nova.request()
                                .get(
                                    `${this.field.apiBaseUrl}/model-options/${parameter.model}`
                                )
                                .then((response) => {
                                    // Get parameter by name from this.parameters
                                    const foundParameter =
                                        this.availableParameters.find(
                                            (item) =>
                                                item.name === parameter.name
                                        );
                                    // Set the options for the parameter.
                                    foundParameter.options = response.data.data;
                                });
                        }
                    }

                    // Remove value.parameters if they don't exist in the availableParameters.
                    this.value.parameters = this.value.parameters.filter(
                        (parameter) =>
                            this.availableParameters.find(
                                (item) => item.name === parameter.name
                            )
                    );
                }
            );

            if (selectedRouteName.startsWith("external.")) {
                let name = "url";
                if (selectedRouteName === "external.mailto") {
                    name = "email";
                } else if (selectedRouteName === "external.tel") {
                    name = "phone";
                }

                const parameter = {
                    name: name,
                    label: name.charAt(0).toUpperCase() + name.slice(1),
                    type: "string",
                    model: null,
                    isRequired: true,
                    options: [],
                    value: "",
                };

                this.availableParameters.push(parameter);

                if (
                    !this.parameters.find(
                        (item) => item.name === parameter.name
                    )
                ) {
                    this.parameters.push({
                        name: parameter.name,
                        value: parameter.value,
                    });
                }
            }

            if (
                selectedRouteName.length &&
                (selectedRouteName === "external.link" ||
                    !selectedRouteName.startsWith("external."))
            ) {
                this.availableOptions = this.field.availableOptions;

                this.availableOptions.forEach((option) => {
                    if (
                        !this.options.find((item) => item.name === option.name)
                    ) {
                        this.options.push({
                            name: option.name,
                            value: option.value,
                        });
                    }
                });
            }

            this.value = this.cleanValue(this.value);
        },
    },

    methods: {
        /*
         * Check if the given value is a valid value object.
         */
        checkIfValidValueObject(value) {
            return (
                typeof value === "object" &&
                value !== null &&
                value.hasOwnProperty("routeName") &&
                value.hasOwnProperty("parameters") &&
                value.hasOwnProperty("options")
            );
        },

        /*
         * Clean the given value object.
         */
        cleanValue(value) {
            return {
                routeName: value.routeName,
                parameters: value.parameters.filter((parameter) =>
                    this.availableParameters.find(
                        (item) => item.name === parameter.name
                    )
                ),
                options: value.options.filter((option) =>
                    this.availableOptions.find(
                        (item) => item.name === option.name
                    )
                ),
            };
        },

        /*
         * Set the initial, internal value for the field.
         */
        setInitialValue() {
            let value = this.field.value;
            // Check if the field value is an object with a route and parameters.
            if (this.checkIfValidValueObject(value)) {
                this.value = value;
            } else {
                // Check if the field value is a valid JSON string.
                try {
                    value = JSON.parse(value);
                    if (this.checkIfValidValueObject(value)) {
                        this.value = value;
                    } else {
                        throw new Error("Not a valid JSON string.");
                    }
                } catch (e) {
                    // If the field value is not a valid JSON string, set the default value.
                    this.value = {
                        routeName: "",
                        parameters: [],
                        options: [],
                    };
                }
            }
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            formData.append(this.field.attribute, JSON.stringify(this.value));
        },
    },
};
</script>
