<template>
    <div
        class="flex flex-col items-center justify-center min-h-screen bg-gray-100"
    >
        <div
            class="w-full max-w-sm bg-black rounded-3xl overflow-hidden shadow-2xl p-4"
        >
            <div
                class="h-32 flex flex-col items-end justify-end p-4 text-white"
            >
                <div class="text-gray-400 text-2xl mb-1">
                    {{ currentExpression || "0" }}
                </div>
                <div class="text-5xl font-light">
                    {{ currentResult || "0" }}
                </div>
            </div>

            <div class="grid grid-cols-4 gap-3 mt-4">
                <button
                    @click="clear"
                    class="rounded-full h-20 text-2xl bg-gray-400 hover:bg-gray-300 active:bg-gray-500 text-black"
                >
                    AC
                </button>
                <button
                    @click="toggleSign"
                    class="rounded-full h-20 text-2xl bg-gray-400 hover:bg-gray-300 active:bg-gray-500 text-black"
                >
                    +/-
                </button>
                <button
                    @click="addOperator('%')"
                    class="rounded-full h-20 text-2xl bg-gray-400 hover:bg-gray-300 active:bg-gray-500 text-black"
                >
                    %
                </button>
                <button
                    @click="addOperator('/')"
                    class="rounded-full h-20 text-2xl bg-orange-500 hover:bg-orange-400 active:bg-orange-600 text-white"
                >
                    ÷
                </button>

                <button
                    @click="addFunction('sqrt(')"
                    class="rounded-full h-20 text-lg bg-gray-400 hover:bg-gray-300 active:bg-gray-500 text-black"
                >
                    √
                </button>
                <button
                    @click="addOperator('^')"
                    class="rounded-full h-20 text-lg bg-gray-400 hover:bg-gray-300 active:bg-gray-500 text-black"
                >
                    x²
                </button>
                <button
                    @click="addChar('(')"
                    class="rounded-full h-20 text-2xl bg-gray-400 hover:bg-gray-300 active:bg-gray-500 text-black"
                >
                    (
                </button>
                <button
                    @click="addChar(')')"
                    class="rounded-full h-20 text-2xl bg-gray-400 hover:bg-gray-300 active:bg-gray-500 text-black"
                >
                    )
                </button>

                <button
                    v-for="n in ['7', '8', '9']"
                    :key="n"
                    @click="addNumber(n)"
                    class="rounded-full h-20 text-2xl bg-gray-700 hover:bg-gray-600 active:bg-gray-800 text-white"
                >
                    {{ n }}
                </button>
                <button
                    @click="addOperator('*')"
                    class="rounded-full h-20 text-2xl bg-orange-500 hover:bg-orange-400 active:bg-orange-600 text-white"
                >
                    ×
                </button>

                <button
                    v-for="n in ['4', '5', '6']"
                    :key="n"
                    @click="addNumber(n)"
                    class="rounded-full h-20 text-2xl bg-gray-700 hover:bg-gray-600 active:bg-gray-800 text-white"
                >
                    {{ n }}
                </button>
                <button
                    @click="addOperator('-')"
                    class="rounded-full h-20 text-2xl bg-orange-500 hover:bg-orange-400 active:bg-orange-600 text-white"
                >
                    -
                </button>

                <button
                    v-for="n in ['1', '2', '3']"
                    :key="n"
                    @click="addNumber(n)"
                    class="rounded-full h-20 text-2xl bg-gray-700 hover:bg-gray-600 active:bg-gray-800 text-white"
                >
                    {{ n }}
                </button>
                <button
                    @click="addOperator('+')"
                    class="rounded-full h-20 text-2xl bg-orange-500 hover:bg-orange-400 active:bg-orange-600 text-white"
                >
                    +
                </button>

                <button
                    @click="addNumber('0')"
                    class="col-span-2 rounded-full h-20 text-2xl bg-gray-700 hover:bg-gray-600 active:bg-gray-800 text-white text-left pl-8"
                >
                    0
                </button>
                <button
                    @click="addNumber('.')"
                    class="rounded-full h-20 text-2xl bg-gray-700 hover:bg-gray-600 active:bg-gray-800 text-white"
                >
                    .
                </button>
                <button
                    @click="calculate"
                    class="rounded-full h-20 text-2xl bg-orange-500 hover:bg-orange-400 active:bg-orange-600 text-white"
                >
                    =
                </button>
            </div>
        </div>

        <div class="mt-6 w-full max-w-sm bg-white rounded-xl shadow-lg p-4">
            <h3 class="text-lg font-semibold mb-3">History</h3>
            <ul class="space-y-2">
                <li
                    v-for="calc in history"
                    :key="calc.id"
                    class="flex justify-between items-center p-2 hover:bg-gray-50 rounded"
                >
                    <span class="text-gray-600"
                        >{{ calc.expression }} =
                        {{ parseFloat(calc.result) }}</span
                    >
                    <button
                        @click="deleteCalculation(calc.id)"
                        class="text-red-500 hover:text-red-600 p-1"
                    >
                        ×
                    </button>
                </li>
            </ul>
            <button
                v-if="history.length"
                @click="clearHistory"
                class="mt-4 w-full py-2 bg-red-500 text-white rounded-lg hover:bg-red-600"
            >
                Clear All
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import * as math from "mathjs";
import axios from "axios";

const currentExpression = ref("");
const currentResult = ref("");
const history = ref([]);
const lastNumber = ref("");
const lastOperator = ref(null);
const isNewCalculation = ref(true);

const addNumber = (num) => {
    if (
        isNewCalculation.value &&
        !isOperator(currentExpression.value.slice(-1))
    ) {
        currentExpression.value = num;
        isNewCalculation.value = false;
    } else {
        currentExpression.value += num;
    }
    lastNumber.value += num;
    calculateLive();
};

const addOperator = (operator) => {
    isNewCalculation.value = false;
    if (currentExpression.value === "") {
        if (operator === "-") {
            currentExpression.value = operator;
        }
        return;
    }

    const lastChar = currentExpression.value.slice(-1);
    if (isOperator(lastChar)) {
        currentExpression.value =
            currentExpression.value.slice(0, -1) + operator;
    } else {
        currentExpression.value += operator;
    }

    lastNumber.value = "";
    lastOperator.value = operator;
};

const addFunction = (func) => {
    isNewCalculation.value = false;
    currentExpression.value += func;
    calculateLive();
};

const addChar = (char) => {
    isNewCalculation.value = false;
    currentExpression.value += char;
    calculateLive();
};

const isOperator = (char) => {
    return ["+", "-", "*", "/", "%", "^"].includes(char);
};

const isBalancedParentheses = (expr) => {
    let count = 0;
    for (let char of expr) {
        if (char === "(") count++;
        if (char === ")") count--;
        if (count < 0) return false;
    }
    return count === 0;
};

const calculateLive = async () => {
    try {
        if (!isBalancedParentheses(currentExpression.value)) {
            return;
        }

        const result = math.evaluate(currentExpression.value);
        if (Number.isFinite(result)) {
            const roundedResult = math.round(result, 8);
            currentResult.value = roundedResult;

            const hasOperator = /[\+\-\*\/\%\^\)sqrt]/.test(
                currentExpression.value
            );

            const lastChar = currentExpression.value.slice(-1);
            if (hasOperator && /[\d)]/.test(lastChar)) {
                await saveCalculation(currentExpression.value, roundedResult);
            }
        }
    } catch (e) {
        console.error("Live calculation error:", e);
    }
};

const saveCalculation = async (expression, result) => {
    try {
        const response = await axios.post("/api/calculator/calculate", {
            expression: expression,
            result: result,
        });
        history.value.unshift(response.data);
    } catch (e) {
        console.error("Error saving calculation:", e);
    }
};

const calculate = async () => {
    if (!currentExpression.value) return;

    try {
        if (!isBalancedParentheses(currentExpression.value)) {
            currentResult.value = "Error: Unbalanced parentheses";
            return;
        }

        const result = math.evaluate(currentExpression.value);
        if (!Number.isFinite(result)) {
            currentResult.value = "Error: Invalid result";
            return;
        }

        const finalResult = math.round(result, 8);
        currentResult.value = finalResult;
        currentExpression.value = finalResult.toString();
        isNewCalculation.value = true;
    } catch (e) {
        currentResult.value = "Error";
        console.error("Calculation error:", e);
    }
};

const clear = () => {
    currentExpression.value = "";
    currentResult.value = "";
    lastNumber.value = "";
    lastOperator.value = null;
    isNewCalculation.value = true;
};

const toggleSign = () => {
    if (currentExpression.value) {
        try {
            const result = math.evaluate(currentExpression.value);
            currentExpression.value = (-result).toString();
            calculateLive();
        } catch (e) {
            console.error("Toggle sign error:", e);
        }
    }
};

const fetchHistory = async () => {
    try {
        const response = await axios.get("/api/calculator/history");
        history.value = response.data;
    } catch (e) {
        console.error("Error fetching history:", e);
    }
};

const deleteCalculation = async (id) => {
    try {
        await axios.delete(`/api/calculator/history/${id}`);
        history.value = history.value.filter((calc) => calc.id !== id);
    } catch (e) {
        console.error("Error deleting calculation:", e);
    }
};

const clearHistory = async () => {
    try {
        await axios.delete("/api/calculator/history");
        history.value = [];
    } catch (e) {
        console.error("Error clearing history:", e);
    }
};

onMounted(() => {
    fetchHistory();
});
</script>
