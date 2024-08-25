import { reactive, computed } from 'vue'

export const store = reactive({
  count: null,
  more_products: null,
  
  
  
})

export const state = reactive({
  cartCounts: getCartCountFromLocalStorage(),
  cartItems: getCartItemsFromLocalStorage(),
  product_id: [],
});

function getCartItemsFromLocalStorage() {
  const items = sessionStorage.getItem('cartItems');
  return items ? items : [];
}

function getCartCountFromLocalStorage() {
  const count = sessionStorage.getItem('cartCounts');
  return count ? parseInt(count, 10) : 0;
}


const addItemToCart = (item) => {
  state.cartItems.push(item);
  setCartItems(state.cartItems);
 
};

function setCartCountToLocalStorage(count) {
  sessionStorage.setItem('cartCounts', count);
}

export const useCartStore = () => {
   const cartCounts = computed(() => state.cartCounts);
   const cartItems = computed(() => state.cartItems);


  function setCartItemsToLocalStorage(items) {
    sessionStorage.setItem('cartItems', JSON.stringify(items) ?? []);
  }


  let integerArray = [];
  let tempIntegerArray = [];

  const setCartCount = () => {

    const countItems = sessionStorage.getItem('cartCounts');
    let integerCount = JSON.parse(countItems);

    integerCount += 1;
    state.cartCounts = integerCount;
    setCartCountToLocalStorage(state.cartCounts);

    
    console.log('COUNT',state.cartCounts);
  };
  const decrementCartCount = () => {
    if (state.cartCounts > 0) {

      const countItems = sessionStorage.getItem('cartCounts');
      let integerCount = JSON.parse(countItems);
  
      integerCount -= 1;
      state.cartCounts = integerCount;
      setCartCountToLocalStorage(state.cartCounts);
      

    }
  };
  
  const setCartItems = (items) => {

    const jsonArrays = sessionStorage.getItem("cartItems");
    let integerArrays = JSON.parse(jsonArrays);
    if (!Array.isArray(integerArrays)) {
        integerArray.push(items);
        tempIntegerArray = integerArray;
    } else {
        integerArrays.push(items);
        tempIntegerArray = integerArrays;
      
    }
    state.cartItems=tempIntegerArray;
    
      setCartItemsToLocalStorage(tempIntegerArray);
   
  };
  const removeItemFromCart = (items) => {

    const jsonArrays = sessionStorage.getItem("cartItems");
    let integerArrays = JSON.parse(jsonArrays);
    if (!Array.isArray(integerArrays)) {
        return;
    } else {
      const index = integerArrays.indexOf(items);
  
      if (index > -1) {
          // Remove the value from the array using splice
          integerArrays.splice(index, 1);
          tempIntegerArray = integerArrays;
      }
      
    }
    state.cartItems=tempIntegerArray;
    
      setCartItemsToLocalStorage(tempIntegerArray);

   
  };
  

 

  const incrementCartCount = () => {
    setCartCount(state.cartCounts + 1);
  };

 

  return {
    cartCounts,
    cartItems,
    setCartCount,
    incrementCartCount,
    decrementCartCount,
    setCartItems,
    addItemToCart,
    removeItemFromCart
  };
}