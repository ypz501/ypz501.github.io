while True:
    try:
        n = list(map(int, input().split()))  
        product = 1
        for i in n:
            product *= i  

        for _ in range(product):
            print("Hello World")  
        print()  

    except EOFError:
        break  