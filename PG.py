n = 0

while True:
    
	try：
    	H = input()
        if H == "Hello World":
    		n += 1
        
	except EOFError:
		break
      
print(f"Hello World * {n}")
