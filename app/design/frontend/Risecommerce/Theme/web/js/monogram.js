const wordInput = document.querySelector('.field.monogram.name input');
            const fontSelect = document.querySelector('.field.monogram.font select');
			const monogram = document.querySelector('.field.monogramoption select');
			const monogramBedding = document.querySelector('.field.monogramoption input[type="radio"]');
            const colorSelect = document.querySelector('.field.monogram.color.tdcolor select');
			const colorSelectDiv = document.querySelector('.field.monogram.color.tdcolor .mageworx-swatch-option.image');
			  
			
			const swatchOptionsFont = document.querySelectorAll('.field.monogram.font .mageworx-swatch-option');
			 if (swatchOptionsFont.length > 0) { 
				swatchOptionsFont[0].style.pointerEvents = 'none';
			}
			const preview = document.getElementById('preview-font');
			const previewContainer = document.getElementById('preview-container');

				function updatePreview() {
                const word = wordInput.value;
                preview.textContent = word;
              
            }
    
			swatchOptionsFont.forEach(option => {
				option.addEventListener('click', function() {
				 var inputFieldMonogram = document.querySelector('.field.required.monogram.name .input-text.product-custom-option');
				const optionLabel = option.getAttribute('data-option-label');
				let fontfamily;
				switch (optionLabel) {
					
			case "1": 
				fontfamily = 'font1';
				inputFieldMonogram.value = "ABCD"; 
				inputFieldMonogram.maxLength = 4;
			break;
			case "2": 
				fontfamily = 'font2';
				inputFieldMonogram.value = "ABCD"; 
				if (inputFieldMonogram.value.length > 1) {
					inputFieldMonogram.value = inputFieldMonogram.value.slice(0, 1); 	
				}
				inputFieldMonogram.maxLength = 1;
			break;
			case "3": 
				fontfamily = 'font3';
				inputFieldMonogram.value = "ABCD"; 
				inputFieldMonogram.maxLength = 4;
			break;
			case "4": 
				fontfamily = 'font4';
				inputFieldMonogram.value = "ABCD"; 
				inputFieldMonogram.maxLength = 4;   
			break;
			case "5": 
				fontfamily = 'font5';
				inputFieldMonogram.value = "ABCD"; 
				if (inputFieldMonogram.value.length > 1) {
					inputFieldMonogram.value = inputFieldMonogram.value.slice(0, 1); 	
				}
				inputFieldMonogram.maxLength = 1;
			break;
			case "6": 
				fontfamily = 'font6';
				inputFieldMonogram.value = "ABCD"; 
				inputFieldMonogram.maxLength = 4;
			break;
			case "7": 
				fontfamily = 'font7';
				inputFieldMonogram.value = "ABCD"; 
				inputFieldMonogram.maxLength = 4;
			break;
			case "8": 
				fontfamily = 'font8';
				inputFieldMonogram.value = "ABCD"; 
				if (inputFieldMonogram.value.length > 1) {
					inputFieldMonogram.value = inputFieldMonogram.value.slice(0, 1); 
				}
				inputFieldMonogram.maxLength = 1;
			break;
			case "9": 
				fontfamily = 'font9';
				inputFieldMonogram.value = "ABCD"; 
				if (inputFieldMonogram.value.length > 1) {
					inputFieldMonogram.value = inputFieldMonogram.value.slice(0, 1); 	
				}
				inputFieldMonogram.maxLength = 1;
			break;
			case "10": 
				fontfamily = 'font10'; 
				inputFieldMonogram.value = "ABCD"; 
				inputFieldMonogram.maxLength = 4;
			break;
			case "11": 
				fontfamily = 'font11';
				inputFieldMonogram.value = "ABCD"; 
				inputFieldMonogram.maxLength = 4;
			break;
			case "12": 
				fontfamily = 'font12';
				inputFieldMonogram.value = "ABCD"; 
				inputFieldMonogram.maxLength = 4;
			break;
			case "16": 
				fontfamily = 'font16';
				inputFieldMonogram.value = "ABCD"; 
				inputFieldMonogram.maxLength = 4;
			break;
			case "19": 
				fontfamily = 'font19';
				inputFieldMonogram.value = "ABCD";  
				if (inputFieldMonogram.value.length > 1) {
					inputFieldMonogram.value = inputFieldMonogram.value.slice(0, 1); 	
				}
				inputFieldMonogram.maxLength = 1;
			break;			
			case "21": 
				fontfamily = 'font21';
				inputFieldMonogram.value = "ABCD"; 
				if (inputFieldMonogram.value.length > 1) {
					inputFieldMonogram.value = inputFieldMonogram.value.slice(0, 1); 	
				}
				inputFieldMonogram.maxLength = 1;
			break;
			case "22": 
				fontfamily = 'font22';
				inputFieldMonogram.value = "ABCD"; 
				if (inputFieldMonogram.value.length > 1) {
					inputFieldMonogram.value = inputFieldMonogram.value.slice(0, 1); 
				}
				inputFieldMonogram.maxLength = 1;
			break;  
			case "23": 
				fontfamily = 'font23';
				inputFieldMonogram.value = "ABCD"; 
				if (inputFieldMonogram.value.length > 1) {
					inputFieldMonogram.value = inputFieldMonogram.value.slice(0, 1); 	
				}
				inputFieldMonogram.maxLength = 1;
			break;
			case "24": 
				fontfamily = 'font24';
				inputFieldMonogram.value = "ABCD"; 
				inputFieldMonogram.maxLength = 4;
			break;
			case "25":  
				fontfamily = 'font25';
				inputFieldMonogram.value = "ABCD"; 
				inputFieldMonogram.maxLength = 4;
			break;
		
			case "26": 
				fontfamily = 'font26';
				inputFieldMonogram.value = "ABCD"; 
				inputFieldMonogram.maxLength = 4;
			break;
			case "27": 
				fontfamily = 'font27';
				inputFieldMonogram.value = "ABCD"; 
				inputFieldMonogram.maxLength = 4;
			break;
			case "30": 
				fontfamily = 'font30';
				inputFieldMonogram.value = "ABCD"; 
				inputFieldMonogram.maxLength = 4;
			break;
			case "31": 
				fontfamily = 'font31';     
				inputFieldMonogram.value = "ABCD"; 
				inputFieldMonogram.maxLength = 4;
			break;  
			case "32": 
				fontfamily = 'font32';
				inputFieldMonogram.value = "ABCD"; 
				if (inputFieldMonogram.value.length > 1) {
					inputFieldMonogram.value = inputFieldMonogram.value.slice(0, 1); 	
				}
				inputFieldMonogram.maxLength = 1;
			break;
			case "33":   
				fontfamily = 'font33';
				inputFieldMonogram.value = "ABCD"; 
				inputFieldMonogram.maxLength = 4;
			break;
			case "34": 
				fontfamily = 'font34';
				inputFieldMonogram.value = "ABCD"; 
				inputFieldMonogram.maxLength = 4;
			break;
			case "35": 
				fontfamily = 'font35';
				inputFieldMonogram.value = "ABCD"; 
				inputFieldMonogram.maxLength = 4;
			break;
			case "37": 
				fontfamily = 'font37';
				inputFieldMonogram.value = "ABCD"; 
				inputFieldMonogram.maxLength = 4;
			break;  
			case "38": 
				fontfamily = 'font38';
				inputFieldMonogram.value = "ABCD";   
				inputFieldMonogram.maxLength = 4;
			break; 
			case "39": 
				fontfamily = 'font39'; 
				inputFieldMonogram.value = "ABCD"; 
				inputFieldMonogram.maxLength = 4;
			break;  

			default:  
				fontfamily = optionLabel
		}		
				 swatchOptionsFont.forEach(opt => opt.style.pointerEvents = '');


				option.style.pointerEvents = 'none';
				preview.style.fontFamily = fontfamily;
			  	updatePreview();
				});
			});	

	   const swatchOptionsColor = document.querySelectorAll('.field.monogram.color.tdcolor .mageworx-swatch-option');

   
		swatchOptionsColor.forEach(option => {
		option.addEventListener('click', function() {
        
        const optionLabel = option.getAttribute('data-option-label');
		let colorCode;
		
		switch (optionLabel) {
			case "Light Blue":
				colorCode = "#aed8e6";
			break;
			case "Light Grey":
				colorCode = "#d2d2d2"; 
			break;
			case "Taupe":
				colorCode = "#dbd4c2";
			break;
			case "Navy Blue":
				colorCode = "#01055c";
			break;
			case "Dark Grey":
				colorCode = "#a9a9a9";
			break;
			case "Burgundy":
				colorCode = "#80011e";
			break;
			case "Hot Pink":
				colorCode = "#fc6ab3";
			break;
			
			case "Lilac":
				colorCode = "#c593c8";
			break;
			
			case "Sage":
				colorCode = "#76805d";
			break;
			
			case "Parrot Green":
				colorCode = "#62b33b";
			break;
			
			case "Light Pink":
				colorCode = "#feb4c1";
			break;
			default:
				colorCode = optionLabel
		}
		
		preview.style.color = colorCode;
		updatePreview(); 
      });  
    });		     
	if(monogram && previewContainer) {
				monogram.addEventListener('change', function() {
		  const selectedOption = monogram.options[monogram.selectedIndex].textContent;
		   if (selectedOption !== "No" && selectedOption !== "-- Please Select --") {
				 previewContainer.style.display ="block";      
				 var observer = new MutationObserver(function(mutations) {  
				mutations.forEach(function(mutation) {  
					var inputField = document.querySelector('.field.required.monogram.name .input-text.product-custom-option');
					if (inputField) {
						inputField.value = "ABCD"; 
						inputField.maxLength = 4;
						observer.disconnect(); 
					} 
				});
			}); 

					observer.observe(document.body, { childList: true, subtree: true });
				 preview.textContent = "ABCD"; 
			} 
		  else {
			   previewContainer.style.display ="none";
		  }
		 
		}); 
	}
	// Handle radio button selection
	if (monogramBedding) {
		const radioButtons = document.querySelectorAll('.field.monogram-bedding input[type="radio"]');
		console.log("Run");
		previewContainer.style.display = "block";      
		const selectedValue = "ABCD"; // Assuming the radio button value is what you want to display

		const inputField = document.querySelector('.field.required.monogram.name .input-text.product-custom-option');
		if (inputField) {
			inputField.value = selectedValue; 
			inputField.maxLength = 4;
			}    
    	preview.textContent = selectedValue; 
	
	}   
	if(wordInput) {
		wordInput.addEventListener('input', updatePreview);
	
	}