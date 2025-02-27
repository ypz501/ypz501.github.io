n = 0

try：
	while input() == "Hello World":
    	n += 1
        
except EOFError:
	break
      
print(f"Hello World * {n}")
