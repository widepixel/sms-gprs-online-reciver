#include <SoftwareSerial.h>
SoftwareSerial gsm(3,4);

long previousMillis = 0;
long interval = 2000;

String IP = "1.1.1.1";
String DOMAIN = "example.com";
bool flag = true;
bool flag2 = true;
int i = 0;
String readString;

void setup() {
  Serial.begin(9600);
  gsm.begin(9600);

  Serial.println("\r\nSTART");
  delay(10);
  gsm.println("ATE0"); //Отключаем эхо

}

void loop() {

  unsigned long currentMillis = millis();

  if(flag){

  
    if(currentMillis - previousMillis > interval) {
          previousMillis = currentMillis; 
          
          String data;
          int dataSize = 0; 
                 
            gsm.println("AT+CMGR=" + (String)i);
            data = gsm.readString();
  
                
          if(!data.startsWith("\r\nERROR")){
        
            data = split(data,',',2);
            data = split(data,'\r\n',1);
            data = urlencode(data);
            dataSize = data.length() + 49+8;
      
            Serial.println("(" + (String)i + ")" + "(" + (String)dataSize + ")" + data); 

            flag = false;
  
            //gprsSend(data, dataSize);
  
              
          }      

          i++;

     }

    
  }


  if(flag2){
       
    gsm.println("AT+TCPCLOSE=1");
    if(gsm.find("OK")){
      Serial.println("1 OK");
      readSerial();
      gsm.println("AT+TCPSETUP=1,1.1.1.1,80");
      gsm.println("AT+TCPSEND=1,62");
      gsm.println("GET /gsm.php?a=hello HTTP/1.1");
      gsm.println("Host: example.com");
      gsm.println("");
      gsm.println("");
      gsm.println("");
      Serial.println("SEND END");
    }

    flag2 = false;
  }


}


void gprsSend(String data, int dataSize){
   
  Serial.println("SEND >>" + (String)dataSize + (String)">" + (String)data);

  /*
  gsm.println("AT+TCPCLOSE=1");
  delay(100);
  readSerial();
  gsm.println("AT+TCPSETUP=1,1.1.1.1,80");
  delay(1000);
  readSerial();
  gsm.println("AT+TCPSEND=1," + (String)dataSize);
  delay(2000);
  readSerial();
  gsm.println("GET /gsm.php?a=" + (String)data + " HTTP/1.1");
  delay(2000);

  gsm.println("Host: example.com");
  delay(2000);

  gsm.println("");
  delay(2000);

  gsm.println("");
  delay(2000);

  gsm.println("");
  delay(3000);
  readSerial();
  Serial.println("SEND END");
  */
}






String split(String data, char separator, int index)
{
  int found = 0;
  int strIndex[] = {0, -1};
  int maxIndex = data.length()-1;

  for(int i=0; i<=maxIndex && found<=index; i++){
    if(data.charAt(i)==separator || i==maxIndex){
        found++;
        strIndex[0] = strIndex[1]+1;
        strIndex[1] = (i == maxIndex) ? i+1 : i;
    }
  }

  return found>index ? data.substring(strIndex[0], strIndex[1]) : "";
}

String urlencode(String str)
{
    String encodedString="";
    char c;
    char code0;
    char code1;
    char code2;
    for (int i =0; i < str.length(); i++){
      c=str.charAt(i);
      if (c == ' '){
        encodedString+= '+';
      } else if (isalnum(c)){
        encodedString+=c;
      } else{
        code1=(c & 0xf)+'0';
        if ((c & 0xf) >9){
            code1=(c & 0xf) - 10 + 'A';
        }
        c=(c>>4)&0xf;
        code0=c+'0';
        if (c > 9){
            code0=c - 10 + 'A';
        }
        code2='\0';
        encodedString+='%';
        encodedString+=code0;
        encodedString+=code1;
        //encodedString+=code2;
      }
      yield();
    }
    return encodedString;
    
}


void gsmFlush(){
  while (gsm.available()) { gsm.read(); delay(5); }
}


void readSerial(){

  while (gsm.available()) {
    delay(2);
    char c = gsm.read();
    readString += c;

  }

  if (readString.length() >0) {

    Serial.println(readString);
    readString="";
 
  }

}

